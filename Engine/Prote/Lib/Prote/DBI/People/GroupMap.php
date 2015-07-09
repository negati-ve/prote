<?php
namespace Prote\DBI\People;
use DIC\Service;

class GroupMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($Uid,$GroupId){
        $this->Db->set_parameters(array($Uid,$GroupId));
        return $this->Db->Insert('INSERT INTO ProtePeopleGroupMap(Uid,GroupId) VALUES(?,?);');
    }

    public function exists($Uid,$GroupId){
        $this->Db->set_parameters(array($Uid,$GroupId));
        if($this->Db->find_one('SELECT count(*) as C from ProtePeopleGroupMap WHERE Uid=? and GroupId=?')->C){
            return 1;
        }
        else{
            return 0;
        }
    }

    //temp code/make groups dynamic.

    public function exists_by_group_name($Uid,$GroupName){
        $this->Db->set_parameters(array($Uid,$GroupName));
        if($this->Db->find_one('SELECT count(*) as C from ProtePeopleGroupMap ppgm Left join ProtePeopleGroup ppg on ppg.Id=ppgm.GroupId WHERE ppgm.Uid=? and ppg.Name=?')->C){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleGroupMap WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleGroupMap` (
                    `Uid` int(255) NOT NULL,
                      `GroupId` int(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleGroupMap`
                     ADD KEY `GroupId` (`GroupId`), ADD KEY `Uid` (`Uid`);";

        $payload3="ALTER TABLE `ProtePeopleGroupMap`
                    ADD CONSTRAINT `GroupMapGroupId` FOREIGN KEY (`GroupId`) REFERENCES `ProtePeopleGroup` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `GroupMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
                    ";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}