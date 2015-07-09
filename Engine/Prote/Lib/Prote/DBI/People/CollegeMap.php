<?php
namespace Prote\DBI\People;
use DIC\Service;

class CollegeMap {
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
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleCollegeMap` (
                  `Uid` int(255) NOT NULL,
                  `CollegeId` int(255) NOT NULL,
                  `BranchId` int(255) NOT NULL,
                  `Usn` varchar(120) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleCollegeMap`
  ADD KEY `Uid` (`Uid`), ADD KEY `CollegeId` (`CollegeId`), ADD KEY `BranchId` (`BranchId`);";

        $payload3="ALTER TABLE `ProtePeopleCollegeMap`
ADD CONSTRAINT `CollegeMapCollegeBranchId` FOREIGN KEY (`BranchId`) REFERENCES `ProtePeopleCollegeBranch` (`Id`),
ADD CONSTRAINT `CollegeMapCollegeId` FOREIGN KEY (`CollegeId`) REFERENCES `ProtePeopleCollege` (`Id`),
ADD CONSTRAINT `CollegeMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`);
                    ";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}