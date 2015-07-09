<?php
namespace Prote\DBI\People;
use DIC\Service;

class Main {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($email,$pwd){
        $this->Db->set_parameters(array($email,$pwd));
        return $this->Db->Insert('INSERT INTO ProtePeople(Email,Pwd) VALUES(?,?);');
    }
    public function exists($email){
    	$this->Db->set_parameters(array($email));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeople WHERE Email=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function reset_password($email,$pwd){
        $this->Db->set_parameters(array($pwd,$email));
        return $this->Db->insert('UPDATE ProtePeople SET Pwd=? WHERE Email=?;');
    }

    public function verify($email,$pwd){
		$this->Db->set_parameters(array($email,$pwd));
    	if($data=$this->Db->find_one('SELECT Id from ProtePeople where Email=? && Pwd=?')){
    		return $data->Id;
    	}else{
    		return 0;
    	}
    }

    public function get_email_by_id($id){
    	$this->Db->set_parameters(array($id));
    	if($data=$this->Db->find_one('SELECT Email from ProtePeople where Id=?')){
    		return $data->Email;
    	}else{
    		return 0;
    	}
    }

    public function get_id_by_email($email){
    	$this->Db->set_parameters(array($email));
    	if($data=$this->Db->find_one('SELECT Id from ProtePeople where Email=?')){
    		return $data->Id;
    	}else{
    		return 0;
    	}
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeple WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
    	$payload1="CREATE TABLE IF NOT EXISTS `ProtePeople` (
					`Id` int(255) NOT NULL,
					`Email` varchar(255) NOT NULL,
					`Pwd` varchar(255) NOT NULL,
					`Handle` varchar(255) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

		$payload2="ALTER TABLE `ProtePeople`
                    ADD PRIMARY KEY (`Id`);
                    ";

		$payload3="ALTER TABLE `ProtePeople`
                    MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT;";

        $payload4="INSERT INTO `ProtePeople` (`Id`, `Email`, `Pwd`, `Handle`) VALUES
(1, 'Prote@Engine.com', '4f50cbfd3bc6bd495615d42a1fb4996d52749b416f1b44dee4bc8b0085846f15f88a5aa37c28ab406a75b883c3d7e8396eb3d33fff631b9213bbaf6b922c1d65', '');";
        
        // $payload5="ALTER TABLE `ProtePeople` ADD `Reset` INT(1) NOT NULL AFTER `Handle`, ADD `ResetToken` VARCHAR(255) NOT NULL AFTER `Reset`;";
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}