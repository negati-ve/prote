<?php
namespace Prote\DBI\Community;
use DIC\Service;

class AdminMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($Uid,$CollegeId,$BranchId,$Usn=''){
        $this->Db->set_parameters(array($Uid,$CollegeId,$BranchId,$Usn));
        return $this->Db->insert('INSERT INTO ProtePeopleCollegeMap(Uid,CollegeId,BranchId,Usn) VALUES(?,?,?,?);');
    }

    public function exists($Uid,$CollegeId){
        $this->Db->set_parameters(array($Uid,$CollegeId));
        if($this->Db->find_one('SELECT count(*) as C from ProtePeopleCollegeMap WHERE Uid=?,CollegeId=?')->C>1){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleCollegeMap WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE `habba.dev`.`ProteCommunityAdminMap` ( `Uid` INT(255) NOT NULL , `CommunityId` INT(255) NOT NULL , `Level` INT NOT NULL DEFAULT '0' ) ENGINE = InnoDB;";

        $payload2="ALTER TABLE `habba.dev`.`ProteCommunityAdminMap` ADD UNIQUE `UidCommunityId` (`Uid`, `CommunityId`) COMMENT 'verifyunique';";

        $payload3="ALTER TABLE `ProteCommunityAdminMap` ADD CONSTRAINT `CommunityAdminUid` FOREIGN KEY (`Uid`) REFERENCES `habba.dev`.`ProtePeople`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `ProteCommunityAdminMap` ADD CONSTRAINT `CommunityAdminCommunityId` FOREIGN KEY (`CommunityId`) REFERENCES `habba.dev`.`ProteCommunity`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}