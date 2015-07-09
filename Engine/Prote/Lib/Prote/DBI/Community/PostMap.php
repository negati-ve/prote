<?php
namespace Prote\DBI\Community;
use DIC\Service;

class Main {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($email,$pwd){
        $this->Db->set_parameters(array($email,$pwd));
        return $this->Db->Insert('INSERT INTO ProtePeople(Email,Pwd) VALUES(?,?);');
    }
    public function exists($email){
    	$this->Db->set_parameters(array($email));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeople WHERE Email=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function reset_password($email,$pwd){
        $this->Db->set_parameters(array($pwd,$email));
        return $this->Db->insert('UPDATE ProtePeople SET Pwd=? WHERE Email=?;');
    }

    public function verify($email,$pwd){
		$this->Db->set_parameters(array($email,$pwd));
    	if($data=$this->Db->find_one('SELECT Id from ProtePeople where Email=? && Pwd=?')){
    		return $data->Id;
    	}else{
    		return 0;
    	}
    }

    public function get_email_by_id($id){
    	$this->Db->set_parameters(array($id));
    	if($data=$this->Db->find_one('SELECT Email from ProtePeople where Id=?')){
    		return $data->Email;
    	}else{
    		return 0;
    	}
    }

    public function get_id_by_email($email){
    	$this->Db->set_parameters(array($email));
    	if($data=$this->Db->find_one('SELECT Id from ProtePeople where Email=?')){
    		return $data->Id;
    	}else{
    		return 0;
    	}
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeple WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
    	$payload1="CREATE TABLE `ProteCommunityPostMap` ( `Uid` INT(255) NOT NULL , `CommunityId` INT(255) NOT NULL , `PostId` INT(255) NOT NULL ) ENGINE = InnoDB;";
        $payload2="ALTER TABLE `habba.dev`.`ProteCommunityPostMap` ADD UNIQUE `CommunityPostBinding` (`CommunityId`, `PostId`) COMMENT '';";

        $payload3="ALTER TABLE `ProteCommunityPostMap` DROP FOREIGN KEY `CommunityPostMapCommunityid`; ALTER TABLE `ProteCommunityPostMap` ADD CONSTRAINT `CommunityPostMapCommunityId` FOREIGN KEY (`CommunityId`) REFERENCES `habba.dev`.`ProteCommunity`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `ProteCommunityPostMap` ADD CONSTRAINT `CommunityPostMapPostId` FOREIGN KEY (`PostId`) REFERENCES `habba.dev`.`ProteCommunityPost`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";

		
       
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}