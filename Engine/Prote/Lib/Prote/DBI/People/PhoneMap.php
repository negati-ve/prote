<?php
namespace Prote\DBI\People;
use DIC\Service;

class PhoneMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($uid,$phoneid){
        $this->Db->set_parameters(array($uid,$phoneid));
        return $this->Db->Insert('INSERT INTO ProtePeoplePhoneMap(Uid,PhoneId) VALUES(?,?);');
    }

    public function add_by_number($uid,$phone){
        $this->Db->set_parameters(array($phone));
        $phoneid=$this->Db->find_one("SELECT Id FROM ProtePeoplePhone where Number=?")->Id;
        if($phoneid){
            $this->Db->set_parameters(array($uid,$phoneid));
           return $this->Db->Insert('INSERT INTO ProtePeoplePhoneMap(Uid,PhoneId) VALUES(?,?);');            
        }else{
            return 0;
        }
    }

    public function exists($uid,$phoneid){
        $this->Db->set_parameters(array($uid,$phoneid));
        if($this->Db->find_one('SELECT count(*) as C from ProtePeoplePhoneMap WHERE Uid=?,PhoneId=?')->C>1){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeoplePhoneMap WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeoplePhoneMap` (
                      `Uid` int(255) NOT NULL,
                      `PhoneId` int(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeoplePhoneMap`
                    ADD KEY `Uid` (`Uid`), ADD KEY `Phone` (`PhoneId`);
                    ";

        $payload3="ALTER TABLE `ProtePeoplePhoneMap`
                    ADD CONSTRAINT `PhoneMapPhoneId` FOREIGN KEY (`PhoneId`) REFERENCES `ProtePeoplePhone` (`Id`),
                    ADD CONSTRAINT `PhoneMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
                    ";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}