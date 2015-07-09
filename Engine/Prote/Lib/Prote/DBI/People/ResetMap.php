<?php
namespace Prote\DBI\People;
use DIC\Service;

class ResetMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($Uid,$Type,$Token){
        $this->Db->set_parameters(array($Uid,$Type,$Token));
        return $this->Db->insert('INSERT INTO ProtePeopleResetMap(Uid,Type,Token) VALUES(?,?,?);');
    }

    public function exists($Uid){
    	$this->Db->set_parameters(array($Uid));

    	if($s=$this->Db->find_one('SELECT count(*) as C from ProtePeopleResetMap WHERE Uid=?')->C){
            return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function remove($Uid){
        $this->Db->set_parameters(array($Uid));
        if($this->Db->query('DELETE FROM ProtePeopleResetMap WHERE Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleResetMap` (
                  `Uid` int(255) NOT NULL,
                  `Type` int(11) NOT NULL,
                  `Token` varchar(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleResetMap` ADD CONSTRAINT `ResetMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople`(`Id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
        $payload3="ALTER TABLE `ProtePeopleResetMap` ADD UNIQUE(`Uid`);";
        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }
    
}