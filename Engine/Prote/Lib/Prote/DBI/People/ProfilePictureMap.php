<?php
namespace Prote\DBI\People;
use DIC\Service;

class ProfilePictureMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($uid,$FileName,$ext){
        $this->Db->set_parameters(array($uid,$FileName,$ext));
        return $this->Db->Insert('INSERT INTO `ProtePeopleProfilePictureMap` (Uid ,FileName ,Ext)VALUES (?,?,?)');
    }

    public function exists($FileName){
        $this->Db->set_parameters(array($FileName));
        if($this->Db->find_one('SELECT count(*) as C from ProtePeopleProfilePictureMap WHERE FileName=?')->C){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleProfilePictureMap WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="
CREATE TABLE IF NOT EXISTS `ProtePeopleProfilePictureMap` (
  `Uid` int(255) NOT NULL,
  `FileName` varchar(255) NOT NULL,
  `Ext` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $payload2="ALTER TABLE `ProtePeopleProfilePictureMap`
  ADD KEY `Uid` (`Uid`);
                    ";

        $payload3="ALTER TABLE `ProtePeopleProfilePictureMap`
ADD CONSTRAINT `ProfilePictureMap` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`);
                    ";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}