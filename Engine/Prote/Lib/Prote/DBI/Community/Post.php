<?php
namespace Prote\DBI\Community;
use DIC\Service;

class Post {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    // public function get_posts($no){
    //     $this->Db->set_parameters(array($no));
    //     $posts=$this->Db->find_many('SELECT T2.*,T1.Id as CId,T1.Sidebar as ComSidebar,T1.Name as ComName,T2.UV-T2.DV as TV From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.Tid WHERE T2.Id is not NULL ORDER BY TV DESC, T1.Created DESC LIMIT ?');
    //     if($posts){
    //     return $posts;
    //     }
    // }

    public function get_posts_new_algo($no){
        $this->Db->set_parameters(array($no));
        $posts=$this->Db->find_many('SELECT ppm.FirstName,T2.*,T1.Id as CId,T1.Sidebar as ComSidebar,T1.Name as ComName,T2.UpVotes-T2.DownVotes as TV,(T2.UpVotes-T2.DownVotes)/(-TIMESTAMPDIFF(HOUR, NOW(), T2.Created)+2)^1.2 as points,(SELECT count(pcpc.PostId) FROM ProteCommunityPostComment pcpc where T2.Id=pcpc.PostId  ) as commentcount From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.CommunityId left join ProtePeopleMeta ppm on ppm.Uid=T2.Uid WHERE T2.Id is NOT NULL ORDER BY points DESC ');
        if($posts){
        return $posts;
        }
    }

    public function get_posts_new_algo_page($pno){
        $s=($pno-1)*12;
        $e=$pno*12;
        $this->Db->set_parameters(array($s,$e));
        $posts=$this->Db->find_many('SELECT ppm.FirstName,ppm.LastName,T2.*,T1.Id as CId,T1.Sidebar as ComSidebar,T1.Name as ComName,T2.UpVotes-T2.DownVotes as TV,(T2.UpVotes-T2.DownVotes)/(-TIMESTAMPDIFF(HOUR, NOW(), T2.Created)+2)^1.2 as points,(SELECT count(pcpc.PostId) FROM ProteCommunityPostComment pcpc where T2.Id=pcpc.PostId  ) as commentcount  From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.CommunityId left join ProtePeopleMeta ppm on ppm.Uid=T2.Uid WHERE T2.Id is NOT NULL ORDER BY points DESC LIMIT ?,?');
        if($posts){
        return $posts;
        }
    }

    public function get_post_by_title($title){
        $this->Db->set_parameters(array($title));
        $posts=$this->Db->find_many('SELECT * From ProteCommunityPost WHERE Title=?');
        if($posts){
        return $posts;
        }
    }

    public function get_post_by_id($id){
        $this->Db->set_parameters(array($id));
        $posts=$this->Db->find_many('SELECT T2.*,T1.Sidebar as ComSidebar,T1.Title as ComTitle,T1.Description as ComDescription,T1.Id as CId,T1.Name as ComName,T2.UpVotes-T2.DownVotes as TV From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.CommunityId WHERE T2.Id is not NULL && T2.Id=?');
        if($posts){
        return $posts[0];
        }
    }

    public function get_posts_by_tid($tid,$limit=10){
        $this->Db->set_parameters(array($tid,$limit));
        $posts=$this->Db->find_many('SELECT *,UpVotes-DownVotes as TV From ProteCommunityPost WHERE CommunityId=? ORDER BY TV DESC, Created DESC LIMIT ?');
        if($posts){
        return $posts;
        }
    }

    public function get_posts_by_tid_new_algo_page($tid,$pno=1){
        $s=($pno-1)*12;
        $e=$pno*12;
        $this->Db->set_parameters(array($tid,$s,$e));
        $posts=$this->Db->find_many('SELECT ppm.FirstName,ppm.LastName,T2.*,T1.Id as CId,T1.Sidebar as ComSidebar,T1.Name as ComName,T2.UpVotes-T2.DownVotes as TV,(T2.UpVotes-T2.DownVotes)/(-TIMESTAMPDIFF(HOUR, NOW(), T2.Created)+2)^1.2 as points,(SELECT count(pcpc.PostId) as commentcount FROM ProteCommunityPostComment pcpc where T2.Id=pcpc.PostId  ) as commentcount From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.CommunityId left join ProtePeopleMeta ppm on ppm.Uid=T2.Uid WHERE T2.Id is NOT NULL and T1.Id=? ORDER BY points DESC LIMIT ?,?');
        if($posts){
            if($posts[0]->CommunityId==NULL)
                return 0;
        return $posts;
        }
    }

    public function get_post_by_slug($slug){
        $this->Db->set_parameters(array($slug));
        $posts=$this->Db->find_many('SELECT T2.*,T1.Sidebar as ComSidebar,T1.Title as ComTitle,T1.Description as ComDescription,T1.Id as CId,T1.Name as ComName,T2.UpVotes-T2.DownVotes as TV From ProteCommunity T1 left join ProteCommunityPost T2 on T1.Id=T2.CommunityId WHERE T2.Id is not NULL && T2.Slug=?');
        // $posts=$this->Db->find_many('SELECT *,UV-DV as TV From ProteCommunityPost WHERE Tid=? && Slug=?');
        if($posts){
        return $posts[0];
        }
    }

    public function create_post($Uid,$Title,$Content='',$Tid=0){
        // duplicate ProteCommunityPost returns 2.
        if($this->get_id_by_title($Title)){
            $status=array("Status"=>2,"Operation"=>"Post/Create");
                return json_encode($status);
        }
        $this->Db->set_parameters(array($Uid,$Title,$this->Service->Html()->clean_url($Title),$Content,$Tid,date('Y-m-d H:i:s',time())));
        $status=$InsertId=$this->Db->insert('INSERT INTO ProteCommunityPost (Uid,Title,Slug,Content,CommunityId,Created) Values(?,?,?,?,?,?)');
        
        if($status){
            $status=array("Status"=>1,"Operation"=>"Post/Create","Id"=>$InsertId);
                return json_encode($status);
        }else
            $status=array("Status"=>0,"Operation"=>"Post/Create");
                return json_encode($status);
    }

    public function duplicate_title($Title,$Tid){
        // if($Tid==get_tid_by_title($Title))
    }

    public function get_tid_by_title($Title){
        $this->Db->set_parameters(array($Title));
        $Id=$this->Db->find_one('SELECT Tid FROM ProteCommunityPost WHERE Title=?');
        return $Id;
    }

    public function get_uid_by_id($Id)
    {
        $this->Db->set_parameters(array($Id));
        $Uid=$this->Db->find_one('SELECT Uid From ProteCommunityPost WHERE Id=?');
        return $Uid;

    }
 
    public function get_all_uid()
    {
        $Uids=$this->Db->find_many('SELECT Uid FROM ProteCommunityPost');
        return $Uids;
    }
    
    public function set_uid_by_id($Uid,$Id)
    {
        $this->Db->set_parameters(array($Uid,$Id));
        $status=$this->Db->query('UPDATE ProteCommunityPost SET Uid=? WHERE Id=?');
        if($status){
            return 1;
        }else
            return 0;
    }

    public function get_id_by_title($Title){
        $this->Db->set_parameters(array($Title));
        $Id=$this->Db->find_one('SELECT Id FROM ProteCommunityPost WHERE Title=?');
        return $Id;
    }


    public function get_title_by_id($Id)
    {
        $this->Db->set_parameters(array($Id));
        $status=$this->Db->query('SELECT Title FROM ProteCommunityPost WHERE Id=?');
        if($status)
            return $Title;
        else
            return 0;
        
    }
    

    public function set_title_by_id($Title,$Id)
    {
        $this->Db->set_parameters(array($Title,$Id,$Title));
        $status=$this->Db->query('INSERT INTO ProteCommunityPost (Title,Id) Values(?,?) On Duplicate key UPDATE ProteCommunityPost SET Title=?');
        if($status)
            return $Title;
        else
            return 0;    
    }

    public function get_content_by_id($Id)
    {
        $this->Db->set_parameters(array($Id));
        $Content=$this->Db->query('SELECT Content FROM ProteCommunityPost WHERE Id=?');
        return $Content;    
    }
    
 
    public function set_content_by_id($Content,$Id)
    {
       $this->Db->set_parameters(array($Id));
        $Title=$this->Db->query('UPDATE ProteCommunityPost SET Title=? WHERE Id=?');
        return $Title;
    }


    public function get_tid_by_id($Tid,$Id)
    {
        $this->Db->set_parameters(array($Id));
        $Tid=$this->Db->query('SELECT Tid FROM ProteCommunityPost WHERE Id=?');
        return $Tid;    
    }
    
    public function set_tid_by_id($Tid,$Id)
    {
        $this->Db->set_parameters(array($Title,$Id,$Title));
        $status=$this->Db->query('INSERT INTO ProteCommunityPost (Tid,Id) Values(?,?) On Duplicate key UPDATE ProteCommunityPost SET Tid=?');
        if($status)
            return $Title;
        else
            return 0;  
    }


    public function downvote($Id)
    {
        //Create loggers to track user activity. 
        //But do we really want to track user activity?
        $result=$this->Service->Prote()->DBI()->Community()->PostVoteMap()->down($this->Service->Auth()->get_uid(),$Id);
        $r=json_decode($result);
        if($r->Status){
            $this->Db->set_parameters(array($Id));
            $status=$this->Db->query('UPDATE ProteCommunityPost SET DownVotes=DownVotes+1 WHERE Id=?');
            if($status){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public function upvote($Id)
    {
        $result=$this->Service->Prote()->DBI()->Community()->PostVoteMap()->up($this->Service->Auth()->get_uid(),$Id);
        $r=json_decode($result);
        if($r->Status){
            $this->Db->set_parameters(array($Id));
            $status=$this->Db->query('UPDATE ProteCommunityPost SET UpVotes=UpVotes+1 WHERE Id=?');
            if($status){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    public function install(){
    	$payload1="CREATE TABLE `habba.dev`.`ProteCommunityPost` ( `Id` INT(255) NOT NULL AUTO_INCREMENT , `Title` TEXT NOT NULL , `Slug` TEXT NOT NULL , `Content` TEXT NOT NULL , `UpVotes` INT(255) NOT NULL , `DownVotes` INT(255) NOT NULL , `Created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `Modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`Id`) ) ENGINE = InnoDB;";

		

        
       
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}