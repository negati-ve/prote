<?php
namespace Prote\DBI\Community;
use DIC\Service;

class PostVoteMap {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function up($Uid,$PostId){
        $this->Db->set_parameters(array($Uid,$PostId));
        if($this->Db->find_one('Select count(*) as c from ProteCommunityPostVoteMap Where Uid=? AND PostId=? AND Vote=-1;')->c){
            //delete my vote.
            //that is neutralize my old vote before my next up vote that actually counts
            $this->Db->set_parameters(array($Uid,$PostId));
            $status=$this->Db->query('DELETE FROM ProteCommunityPostVoteMap Where Uid=? AND PostId=? AND Vote=-1;');             
            if($status){
                $status=array("Status"=>1,"Operation"=>"Neutralize");
                return json_encode($status);
            }else{
                $status=array("Status"=>0,"Operation"=>"Neutralize");
                return json_encode($status);
            }
        }
        
        $this->Db->set_parameters(array($Uid,$PostId,1));
        $status=$this->Db->query('INSERT INTO ProteCommunityPostVoteMap(Uid,PostId,Vote) VALUES(?,?,?);');
        if($status){
            $status=array("Status"=>1,"Operation"=>"UpVote");
            return json_encode($status);

        }else{
            $status=array("Status"=>0,"Operation"=>"UpVote");
            return json_encode($status);
        }
    }

    public function down($Uid,$PostId){
        $this->Db->set_parameters(array($Uid,$PostId));
        if($this->Db->find_one('Select count(*) as c from ProteCommunityPostVoteMap Where Uid=? AND PostId=? AND Vote=1;')->c){
            //delete my vote.
            //that is neutralize my old vote before my next down vote that actually counts
            $this->Db->set_parameters(array($Uid,$PostId));
            $status=$this->Db->query('DELETE FROM ProteCommunityPostVoteMap Where Uid=? AND PostId=? AND Vote=1;');             
            if($status){
                $status=array("Status"=>1,"Operation"=>"Neutralize");
                return json_encode($status);
            }else{
                $status=array("Status"=>0,"Operation"=>"Neutralize");
                return json_encode($status);
            }
        }
        
        $this->Db->set_parameters(array($Uid,$PostId,-1));
        $status=$this->Db->query('INSERT INTO ProteCommunityPostVoteMap(Uid,PostId,Vote) VALUES(?,?,?);');
        if($status){
            $status=array("Status"=>1,"Operation"=>"DownVote");
            return json_encode($status);

        }else{
            $status=array("Status"=>0,"Operation"=>"DownVote");
            return json_encode($status);
        }
    }

    // public function down($Uid,$PostId){
    //     $this->Db->set_parameters(array($Uid,$PostId,-1));
    //     $status=$this->Db->query('INSERT INTO ProteCommunityPostVoteMap(Uid,PostId,Vote) VALUES(?,?,?);');
    //     if($status){
    //         return 1;
    //     }else{
    //         return 0;
    //     }
    // }

    public function voted($Uid,$PostId){
        $this->Db->set_parameters(array($Uid,$PostId));
        $vote=$this->Db->find_one('SELECT count(*) as count,Vote FROM ProteCommunityPostVoteMap WHERE Uid=? && PostId=?');
        if($vote->count){
            return $vote;
        }
        else{
            return false;
        }

    }

    public function total_votes_by_user($Uid){
        $this->Db->set_parameters(array($Uid));
        $vote=$this->Db->query('SELECT count(*) as count FROM PostVotes WHERE Uid=?');
        if($vote->count){
            return $vote->count;
        }
        else{
            return false;
        }
    }

    public function total_votes_by_post($Pid){
        $this->Db->set_parameters(array($Pid));
        $vote=$this->Db->query('SELECT count(*) as count FROM PostVotes WHERE Pid=?');
        if($vote->count){
            return $vote->count;
        }
        else{
            return false;
        }
    }

    public function install(){
    	$payload1="CREATE TABLE IF NOT EXISTS `ProteCommunityPostVoteMap` (
                      `Uid` int(255) NOT NULL,
                      `PostId` int(255) NOT NULL,
                      `Vote` int(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$payload2="ALTER TABLE `ProteCommunityPostVoteMap` ADD UNIQUE KEY `UidPostVote` (`Uid`,`PostId`), ADD KEY `CommunityPostVoteMapPostId` (`PostId`);";
        $payload3="ALTER TABLE `ProteCommunityPostVoteMap`
ADD CONSTRAINT `CommunityPostVoteMapPostId` FOREIGN KEY (`PostId`) REFERENCES `ProteCommunityPost` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `CommunityPostVoteMapUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}