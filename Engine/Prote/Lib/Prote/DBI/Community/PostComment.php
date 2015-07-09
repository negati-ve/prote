<?php
namespace Prote\DBI\Community;
use DIC\Service;

class PostComment {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function get_comments_by_pid($Pid){
        $this->Db->set_parameters(array($Pid));
        // $posts=$this->Db->find_many('SELECT * From Comments WHERE Pid=? ORDER BY Created DESC LIMIT ?');
       // $posts=$this->Db->findmany('SELECT T1.*, GROUP_CONCAT(T2.Replyto,T2.Comment) FROM Comments T1 LEFT JOIN Comments T2 ON T1.Id = T2.Replyto WHERE T1.Parent = 1 GROUP BY T1.Replyto ');
        $posts=$this->Db->find_many('SELECT a.*,b.Id as reply_id,b.Uid as reply_from,concat(d.FirstName," ",d.LastName) as reply_name, b.Comment as reply_comment,b.ParentCommentId as reply_parent, b.UpVotes as reply_uv,b.DownVotes as reply_dv,b.Modified as reply_modified,concat(c.Firstname," ",c.Lastname)as name FROM ProteCommunityPostComment a LEFT JOIN ProteCommunityPostComment b ON a.Id = b.ReplyToCommentId LEFT JOIN ProtePeopleMeta c ON a.Uid=c.Uid LEFT JOIN ProtePeopleMeta d on b.Uid=d.Uid WHERE a.PostId=?');
        if($posts){
        return $posts;
        }
    }

    public function create_comment($Uid,$Pid,$Comment,$Replyto=0){
        // duplicate post returns 2.
        // if($this->get_id_by_title($Title)){
        //     return 2;
        // }
        $this->Db->set_parameters(array($Uid,$Pid,$Comment,$Replyto,date('Y-m-d H:i:s',time())));
        $status=$this->Db->query('INSERT INTO ProteCommunityPostComment (Uid,PostId,Comment,ReplyToCommentId,Created) Values(?,?,?,?,?)');
        if($Replyto){
        $this->Db->set_parameters(array($Replyto));
        $this->Db->query('UPDATE ProteCommunityPostComment SET ParentCommentId=1 WHERE Id=?');
        }
        if($status){
            return 1;
        }else{
            return 0;
        }
    }

    public function get_comment_by_id($id){
        $this->Db->set_parameters(array($id));
        $Id=$this->Db->find_one('SELECT Comment FROM ProteCommunityPostComment WHERE Id=?');
        return $Id;
    }

    public function get_comments_by_uid($Uid,$no=5)
    {
        $this->Db->set_parameters(array($Uid,$no));
        $Uid=$this->Db->find_one('SELECT Comment From ProteCommunityPostComment WHERE Uid=? LIMIT ?');
        return $Uid;

    }
 
 
    public function upvote($id){
        $this->Db->set_parameters(array($id));
        $status=$this->Db->query('UPDATE ProteCommunityPostComment SET UpVotes=UpVotes+1 WHERE Id=?');
        return $status;
    }

    public function downvote($id){
        $this->Db->set_parameters(array($id));
        $status=$this->Db->query('UPDATE ProteCommunityPostComment SET DownVotes=DownVotes+1 WHERE Id=?');
        return $status;
    }

    public function install(){
    	$payload1="CREATE TABLE `habba.dev`.`ProteCommunityPostComment` ( `Id` INT(255) NOT NULL , `Comment` TEXT NOT NULL , `UpVotes` INT(255) NOT NULL , `DownVotes` INT(255) NOT NULL , `Created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `Modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`Id`) ) ENGINE = InnoDB;";
       
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}