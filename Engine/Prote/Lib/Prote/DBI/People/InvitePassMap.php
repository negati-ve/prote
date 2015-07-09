<?php
namespace Prote\DBI\People;
use DIC\Service;

class InvitePassMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($Uid,$Code,$Level,$ValidThru,$Credit){
        $this->Db->set_parameters(array($Uid,$Code,$Level,$ValidThru,$Credit));
        return $this->Db->Insert('INSERT INTO ProtePeopleInvitePassMap(Uid,Code,Level,ValidThru,Credit) VALUES(?,?,?,?,?);');
    }

    public function exists($Uid,$Code){
    	$this->Db->set_parameters(array($Uid,$Code));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeopleInvitePassMap WHERE Uid=? AND Code=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function decrement_credit($code){
        $this->Db->set_parameters(array($code));
        if($data=$this->Db->query('UPDATE ProtePeopleInvitePassMap SET Credit=Credit-1 where Code=?')){
            return $data;
        }else{
            return 0;
        }
    }

    public function get_code_by_uid($Uid){
        $this->Db->set_parameters(array($Uid));
        if($data=$this->Db->find_many('SELECT * from ProtePeopleInvitePassMap where Uid=?')){
            return $data;
        }else{
            return 0;
        }
    }

    public function get_remaining_credits($Code){
        $this->Db->set_parameters(array($Code));
        if($data=$this->Db->find_one('SELECT Credit from ProtePeopleInvitePassMap where Code=?')){
            return $data->Credit;
        }else{
            return 0;
        }
    }

    public function get_uid_by_code($Code){
        $this->Db->set_parameters(array($Code));
        if($data=$this->Db->find_one('SELECT Uid from ProtePeopleInvitePassMap where Code=?')){
            return $data->Uid;
        }else{
            return 0;
        }
    }

    public function remove($Uid,$Code){
        $this->Db->set_parameters(array($Uid,$Code));
        if($this->Db->query('DELETE FROM ProtePeopleInvitePassMap WHERE Uid=? AND Code=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleInvitePassMap` (
                      `Uid` int(255) NOT NULL,
                      `Code` varchar(25) NOT NULL,
                      `Level` int(5) NOT NULL,
                      `ValidThru` date NOT NULL,
                      `Credit` int(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleInvitePassMap`
                    ADD UNIQUE KEY `Code` (`Code`), ADD KEY `Uid` (`Uid`);
                    ";

        $payload3="ALTER TABLE `ProtePeopleInvitePassMap`
                    ADD CONSTRAINT `InvitePassMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";

        $payload4="INSERT INTO `habba`.`ProtePeopleInvitePassMap` (`Uid`, `Code`, `Level`, `ValidThru`, `Credit`) VALUES ('1', 'tesla7700', '2', '2015-02-02', '5');";
        $payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }

}