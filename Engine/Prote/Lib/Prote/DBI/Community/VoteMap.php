<?php
namespace Prote\DBI\Community;
use DIC\Service;

class VoteMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function up($Uid,$CommunityId){
        $this->Db->set_parameters(array($Uid,$CommunityId));
        if($this->Db->find_one('Select count(*) as c from ProteCommunityVoteMap Where Uid=? AND CommunityId=? AND Vote=-1;')->c){
            //delete my vote.
            //that is neutralize my old vote before my next up vote that actually counts
            $this->Db->set_parameters(array($Uid,$CommunityId));
            $status=$this->Db->query('DELETE FROM ProteCommunityVoteMap Where Uid=? AND CommunityId=? AND Vote=-1;');             
            if($status){
                $status=array("Status"=>1,"Operation"=>"Neutralize");
                return json_encode($status);
            }else{
                $status=array("Status"=>0,"Operation"=>"Neutralize");
                return json_encode($status);
            }
        }
        
        $this->Db->set_parameters(array($Uid,$CommunityId,1));
        $status=$this->Db->query('INSERT INTO ProteCommunityVoteMap(Uid,CommunityId,Vote) VALUES(?,?,?);');
        if($status){
            $status=array("Status"=>1,"Operation"=>"UpVote");
            return json_encode($status);

        }else{
            $status=array("Status"=>0,"Operation"=>"UpVote");
            return json_encode($status);
        }
    }

    public function down($Uid,$CommunityId){
        $this->Db->set_parameters(array($Uid,$CommunityId));
        if($this->Db->find_one('Select count(*) as c from ProteCommunityVoteMap Where Uid=? AND CommunityId=? AND Vote=1;')->c){
            //delete my vote.
            //that is neutralize my old vote before my next down vote that actually counts
            $this->Db->set_parameters(array($Uid,$CommunityId));
            $status=$this->Db->query('DELETE FROM ProteCommunityVoteMap Where Uid=? AND CommunityId=? AND Vote=1;');             
            if($status){
                $status=array("Status"=>1,"Operation"=>"Neutralize");
                return json_encode($status);
            }else{
                $status=array("Status"=>0,"Operation"=>"Neutralize");
                return json_encode($status);
            }
        }
        
        $this->Db->set_parameters(array($Uid,$CommunityId,-1));
        $status=$this->Db->query('INSERT INTO ProteCommunityVoteMap(Uid,CommunityId,Vote) VALUES(?,?,?);');
        if($status){
            $status=array("Status"=>1,"Operation"=>"DownVote");
            return json_encode($status);

        }else{
            $status=array("Status"=>0,"Operation"=>"DownVote");
            return json_encode($status);
        }
    }

    // public function up($Uid,$CommunityId){
    //     $this->Db->set_parameters(array($Uid,$CommunityId,1));
    //     $status=$this->Db->query('INSERT INTO ProteCommunityVoteMap(Uid,CommunityId,Vote) VALUES(?,?,?);');
    //     if($status){
    //         return 1;
    //     }else{
    //         return 0;
    //     }
    // }

    // public function down($Uid,$CommunityId){
    //     $this->Db->set_parameters(array($Uid,$CommunityId,-1));
    //     $status=$this->Db->query('INSERT INTO ProteCommunityVoteMap(Uid,CommunityId,Vote) VALUES(?,?,?);');
    //     if($status){
    //         return 1;
    //     }else{
    //         return 0;
    //     }
    // }

    public function exists($Uid,$CommunityId){
        $this->Db->set_parameters(array($Uid,$CommunityId));
        if($this->Db->find_one('SELECT count(*) as C from ProteCommunityVoteMap WHERE Uid=?,CommunityId=?')->C>1){
            return 1;
        }
        else{
            return 0;
        }
    }

    public function remove($Uid,$CommunityId){
        $this->Db->set_parameters(array($Uid,$CommunityId));
        if($this->Db->query('DELETE FROM ProteCommunityVoteMap WHERE Uid=?,CommunityId=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE `habba.dev`.`ProteCommunityVoteMap` ( `Uid` INT(255) NOT NULL , `CommunityId` INT(255) NOT NULL , `Vote` BOOLEAN NOT NULL ) ENGINE = InnoDB;";

        $payload2="ALTER TABLE `ProteCommunityVoteMap` ADD `Created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;";

        $payload3="ALTER TABLE `habba.dev`.`ProteCommunityVoteMap` DROP INDEX `UidComIdUnique`, ADD UNIQUE `UidComIdUnique` (`Uid`, `CommunityId`, `Vote`) COMMENT '';";

        $payload4="ALTER TABLE `ProteCommunityVoteMap` ADD CONSTRAINT `CommunityVoteUid` FOREIGN KEY (`Uid`) REFERENCES `habba.dev`.`ProtePeople`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `ProteCommunityVoteMap` ADD CONSTRAINT `CommunityVoteCommunityid` FOREIGN KEY (`CommunityId`) REFERENCES `habba.dev`.`ProteCommunity`(`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";
        $payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }

}