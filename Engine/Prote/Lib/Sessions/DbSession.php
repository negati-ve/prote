<?php
namespace Sessions;
use DIC\Service,
Etc\Config,
SessionHandlerInterface;
class DbSession implements SessionHandlerInterface{

    private $SavePath;
    private $SessionId;
    private $Db;
    private $Unique;
    private $Data;

    public function __construct(Service $Service){
        $this->Db=$Service->Database();
        $this->Db->connect();
    }

    public function open($save_path,$name){
        
    }

    public function close()
    {
        return $this->Db->last();
    }

    public function read($id)
    {
        $sql = "SELECT session_data FROM ProteSession where session_id =?";
        $this->Db->set_parameters(array($id));
        $data=$this->Db->find_one($sql);
        if($data)
            return $data->session_data;
        else
            $this->Unique=true;
    }

    public function write($id, $data)
    {
        
        if($this->Unique){
          
        $sql = "insert INTO ProteSession SET session_data=?, session_id =?, session_lastaccesstime = CURRENT_TIMESTAMP";
        $this->Db->set_parameters(array($data,$id));
        }
        else{
            session_decode($data);
            if(!isset($_SESSION['id'])){
                $sql = "UPDATE ProteSession SET session_data=?, session_lastaccesstime = CURRENT_TIMESTAMP where session_id=?";
                $this->Db->set_parameters(array($data,$id));

            }
           else{
                $sql = "UPDATE ProteSession SET session_data=?, session_lastaccesstime = CURRENT_TIMESTAMP where session_id=?;UPDATE People SET last_seen=CURRENT_TIMESTAMP() WHERE id=?";
                $this->Db->set_parameters(array($data,$id,$_SESSION['id']));
           }

        }
        $this->Db->insert($sql);    

    }

    public function destroy($id)
    {
        $sql = "DELETE FROM ProteSession WHERE session_id =? OR session_id='' OR session_data='' OR session_lastaccesstime < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 50 MINUTE)";
        $this->Db->set_parameters(array($id));        
        $this->Db->query($sql);
    }

    public function gc($maxlifetime)
    {
        $sql = "DELETE FROM ProteSession WHERE session_id='' OR session_data='' OR session_lastaccesstime < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 50 MINUTE)";
        // $this->Db->set_parameters(array($maxlifetime)); 
        $this->Db->query($sql);
    }

    public function user_is_unique(){
        if(!session_id())
            return $this->Unique=true;
    }

    public function preventHijacking(){
        if(!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent']))
            return false;

        if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
            return false;

        if( $_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
            return false;

        return true;
        }

    public function count(){
        return $this->Db->find_one('SELECT count(*) as C from ProteSession')->C;
    }

    public function session_repair($name="PHPSESSID"){
                session_start();
                session_destroy();
                session_start();  
                session_regenerate_id();
                return session_id();
            
        }

    public function install(){
            $payload1="CREATE TABLE IF NOT EXISTS `ProteSession` (
                      `session_id` varchar(255) NOT NULL DEFAULT '',
                      `session_data` text NOT NULL,
                      `session_lastaccesstime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
                    ";
            $payload2="ALTER TABLE `ProteSession`
                        ADD PRIMARY KEY (`session_id`);";
            $payloads=array($payload1,$payload2);
            $this->Db->drop_payload($payloads,$this);
        }
}

 ?>