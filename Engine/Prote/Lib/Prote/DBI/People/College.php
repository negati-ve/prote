<?php
namespace Prote\DBI\People;
use DIC\Service;

class College {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($name,$code='',$city=''){
        $this->Db->set_parameters(array($name,$code,$city));
        return $this->Db->Insert('INSERT INTO ProtePeopleCollege(Name,Code,City) VALUES(?,?,?);');
    }



    public function exists($name){
    	$this->Db->set_parameters(array($name));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeopleCollege WHERE Name=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function name_to_id($name){
        $this->Db->set_parameters(array($name));
        if($data=$this->Db->find_one('SELECT Id from ProtePeopleCollege WHERE Name=?')->Id){
            return $data;
        }
        else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleCollege WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleCollege` (
                      `Id` int(255) NOT NULL,
                      `Name` varchar(255) NOT NULL,
                      `Code` varchar(255) NOT NULL,
                      `City` varchar(255) NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
                    ";

        $payload2="INSERT INTO `ProtePeopleCollege` (`Id`, `Name`, `Code`, `City`) VALUES (1, 'Unknown', '0', '0');";
        $payload3="ALTER TABLE `ProtePeopleCollege`
                    ADD PRIMARY KEY (`Id`);";
        $payload4="ALTER TABLE `ProtePeopleCollege`
                    MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;";
        $payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
    
}