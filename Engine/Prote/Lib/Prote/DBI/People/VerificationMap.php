<?php
namespace Prote\DBI\People;
use DIC\Service;

class VerificationMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($uid,$type,$value,$verified=0){
        $this->Db->set_parameters(array($uid,$type,$value,$verified));
        return $this->Db->Insert('INSERT INTO ProtePeopleVerificationMap(Uid,Type,Value,Verified) VALUES(?,?,?,?);');
    }

    // public function exists($uid,$phoneid){
    //     $this->Db->set_parameters(array($uid,$phoneid));
    //     if($this->Db->find_one('SELECT count(*) as C from ProtePeopleVerificationMap WHERE Uid=?,PhoneId=?')->C>1){
    //         return 1;
    //     }
    //     else{
    //         return 0;
    //     }
    // }

    // public function remove($id){
    //     $this->Db->set_parameters(array($id));
    //     if($this->Db->query('DELETE FROM ProtePeopleVerificationMap WHERE Id=?')){
    //         return 1;
    //     }else{
    //         return 0;
    //     }
    // }

    public function get($Uid){
        $this->Db->set_parameters(array($Uid));
        if($data=$this->Db->find_many('SELECT * from ProtePeopleVerificationMap where Uid=?')){
            return $data;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleVerificationMap` (
                      `Uid` int(255) NOT NULL,
                      `Type` varchar(255) NOT NULL,
                      `Value` text NOT NULL,
                      `Verified` int(1) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleVerificationMap`
                    ADD KEY `Uid` (`Uid`);
                    ";

        $payload3="ALTER TABLE `ProtePeopleVerificationMap`
                    ADD CONSTRAINT `VerificationMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
                    ";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}