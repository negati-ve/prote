<?php
namespace Prote\DBI\People;
use DIC\Service;

class Group {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function get_by_name($gname){
        $this->Db->set_parameters(array('%'.$gname.'%'));
        return $this->Db->find_many('SELECT * FROM `ProtePeopleGroup` WHERE `Name` LIKE ?');
    }

    public function create($name,$privilege){
        $this->Db->set_parameters(array($name,$privilege));
        return $this->Db->Insert('INSERT INTO ProtePeopleGroup(Name,Privilege) VALUES(?,?);');
    }

    public function exists($name){
    	$this->Db->set_parameters(array($name));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeopleGroup WHERE Name=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function get_by_uid($id){
        $this->Db->set_parameters(array($id));
        if($data=$this->Db->find_one('SELECT distinct(ppg.Name),ppg.Id from ProtePeopleGroupMap ppgm left join ProtePeopleGroup ppg ON ppg.Id=ppgm.GroupId where ppgm.Uid=?')){
            return $data;
        }else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleGroup WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleGroup` (
                    `Id` int(255) NOT NULL,
                      `Name` varchar(255) NOT NULL,
                      `Privilege` varchar(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $payload11="INSERT INTO `ProtePeopleGroup` (`Id`, `Name`, `Privilege`) VALUES (1, 'Others', 'basic'),(2, 'Student', 'basic'),(3, 'Professor', 'basic');";

        $payload2="ALTER TABLE `ProtePeopleGroup`
                    ADD PRIMARY KEY (`Id`);
                    ";
        $payload3="ALTER TABLE `ProtePeopleGroup`
                    MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT;";

        $payloads=(array($payload1,$payload11,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }
    
}