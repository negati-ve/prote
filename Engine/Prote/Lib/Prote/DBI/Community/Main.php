<?php
namespace Prote\DBI\Community;
use DIC\Service;

class Main {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$Service->Database();
        $this->Db->connect();
        //Community Config Variables.
        //For Godsake Create a stable config system for the communities?
 
    }

    public function get_communities($no){
        $this->Db->set_parameters(array($no));
        $Community=$this->Db->find_many('SELECT *,UpVotes-DownVotes as TV From ProteCommunity WHERE Visibility="Public" ORDER BY TV,Created DESC LIMIT ?');
        if($Community){
        return $Community;
        }
    }

    public function get_all_communities(){
        $this->Db->mode=0;
        $Community=$this->Db->find_many('SELECT * From ProteCommunity WHERE Visibility="Public"');
        return ($Community);
    }

    public function get_all_communities_for_autocomplete(){
        $this->Db->mode=0;
        $Community=$this->Db->find_many('SELECT Name as value,Id as data From ProteCommunity WHERE Visibility="Public"');
        return json_encode($Community);
    }

    public function create_community($Uid,$Name,$Title,$Description,$Sidebar,$Type='Basic',$Parent=0){
        $this->Db->set_parameters(array($Name,$Title,$Description,$Sidebar,$Type,$Parent));
        //CREATE THE ProteCommunity
        //MAP IT.
        //MUST BE MAPPED.
        $result=array();
        $status=$this->Db->insert('INSERT INTO ProteCommunity (Name,Title,Description,Sidebar,Type,Parent) Values(?,?,?,?,?,?)');
          
        if($status){
            $result=array("Status"=>1,"Id"=>$status,"Operation"=>"CommunityCreate");
            $this->Db->set_parameters(array($Uid,$status,$level=1000));
            $status1=$this->Db->query('INSERT INTO ProteCommunityAdminMap (Uid,CommunityId,Level) Values(?,?,?)');
            if($status1){
                $result=json_encode($result);
                return $result;
            }else{
                $result=array("Status"=>0,"Operation"=>"CommunityAddAdmin");
                $result=json_encode($result);
                return $result;
            }
            
        }else
            $result=array("Status"=>0,"Operation"=>"CommunityCreate");
            $result=json_encode($result);
            return $result;
    }

    public function update_event($Id,$Name,$Title,$Description,$Sidebar,$Type,$Background=''){
        $this->Db->set_parameters(array($Name,$Title,$Description,$Sidebar,$Type,$Background,$Id));
        //CREATE THE ProteCommunity
        //MAP IT.
        //MUST BE MAPPED.
        $result=array();
        $status=$this->Db->query('UPDATE ProteCommunity Set Name=?,Title=?,Description=?,Sidebar=?,Type=?,Background=? where Id=?');
          
        if($status){
            $result=array("Status"=>1,"Id"=>$status,"Operation"=>"CommunityUpdate");
            // $this->Db->set_parameters(array($Uid,$status,$level=1000));
            // $status1=$this->Db->query('INSERT INTO ProteCommunityAdminMap (Uid,CommunityId,Level) Values(?,?,?)');
            // if($status1){
            //     $result=json_encode($result);
            //     return $result;
            // }else{
            //     $result=array("Status"=>0,"Operation"=>"CommunityAddAdmin");
            //     $result=json_encode($result);
            //     return $result;
            // }
            
        }else
            $result=array("Status"=>0,"Operation"=>"CommunityEdit");
            $result=json_encode($result);
            return $result;
    }


    public function get_owner_by_id($Id)
    {
        $this->Db->set_parameters(array($Id));
        $Uid=$this->Db->find_one('SELECT Owner From ProteCommunity WHERE Id=?');
        return $Uid;

    }

    public function get_id_by_name_for_autocomplete($name){
        $this->Db->set_parameters(array("%".$name."%"));
        $data=$this->Db->find_many('SELECT Id,Name FROM ProteCommunity WHERE Name LIKE ?');
        if($data)
            return $data;
        else
            return 0;
    }
    
    public function get_id_by_name($name){
        $this->Db->set_parameters(array($name));
        $data=$this->Db->find_one('SELECT Id FROM ProteCommunity WHERE Name=?');
        if($data)
            return $data->Id;
        else
            return 0;
    }

    public function get_name_by_id($id){
        $this->Db->set_parameters(array($name));
        $data=$this->Db->find_one('SELECT Name FROM ProteCommunity WHERE Id=?');
        if($data)
            return $data->Id;
        else
            return 0;
    }

    public function get_all_by_name($name){
        $this->Db->set_parameters(array($name));
        $Id=$this->Db->find_one('SELECT * FROM ProteCommunity WHERE Name=?');
        return $Id;
    }

    public function get_all_by_id($id){
        $this->Db->set_parameters(array($id));
        $Id=$this->Db->find_one('SELECT * FROM ProteCommunity WHERE Id=?');
        return $Id;
    }

    public function get_id_by_title($Title){
        $this->Db->set_parameters(array($Title));
        $Id=$this->Db->find_one('SELECT Id FROM ProteCommunity WHERE Title=?');
        return $Id;
    }


    public function get_title_by_id($Id)
    {
        $this->Db->set_parameters(array($Id));
        $status=$this->Db->find_one('SELECT Title FROM ProteCommunity WHERE Id=?');
        if($status)
            return $status->Title;
        else
            return 0;
        
    }
    

    public function change_title($Id,$Title)
    {
        $this->Db->set_parameters(array($Title,$Id));
        $status=$this->Db->query('UPDATE ProteCommunity SET Title=? WHERE Id=?');
        if($status)
            return 1;
        else
            return 0;
    }
    
    public function transfer_ownership($Id,$Owner)
    {
        $this->Db->set_parameters(array($Id,$Owner));
        $status=$this->Db->query('UPDATE ProteCommunity SET Id=?, Owner=?');
        if($status)
            return 1;
        else
            return 0;  
    }

    public function is_parent($Id){
        $this->Db->set_parameters(array($Id));
        $status=$this->Db->find_one('SELECT Type FROM ProteCommunity WHERE Id=?')->Type;
        if($status=="BasicParent")
            return 1;
        else
            return 0;
    }

    public function get_children($Id){
        $status=$this->Db->find_many('SELECT * FROM ProteCommunity WHERE Parent=?');
        if($status)
            return $status;
        else
            return 0;
    }

    public function link($parent,$child)
    {
        $this->Db->set_parameters(array($parent,$child));
        $status=$this->Db->query('UPDATE ProteCommunity SET Parent=? WHERE Id=?');
        if($status){
            $this->Db->set_parameters(array('BasicParent',$parent));
            $status=$this->Db->query('UPDATE ProteCommunity SET Type=? WHERE Id=?');
            return 1;
        }
        else
            return 0;
    }
    // public function downvote($Id)
    // {
    //     $status=$this->Service->Prote()->DBI()->Community()->VoteMap()->down($this->Service->Auth()->get_uid(),$Id);
    //     if($status){
    //         $this->Db->set_parameters(array($Id));
    //         $status=$this->Db->query('UPDATE ProteCommunity SET DownVotes=DownVotes+1 WHERE Id=?');
    //         if($status){
    //             return 1;
    //         }else{
    //             return 0;
    //         }
    //     }
    // }


    // public function upvote($Id)
    // {
    //     $status=$this->Service->Prote()->DBI()->Community()->VoteMap()->up($this->Service->Auth()->get_uid(),$Id);
    //     if($status){
    //         $this->Db->set_parameters(array($Id));
    //         $status=$this->Db->query('UPDATE ProteCommunity SET UpVotes=UpVotes+1 WHERE Id=?');
    //         if($status){
    //             return 1;
    //         }else{
    //             return 0;
    //         }
    //     }
    // }

    public function downvote($Id)
    {
        //Create loggers to track user activity. 
        //But do we really want to track user activity?
        $result=$this->Service->Prote()->DBI()->Community()->VoteMap()->down($this->Service->Auth()->get_uid(),$Id);
        $r=json_decode($result);
        if($r->Status){
            $this->Db->set_parameters(array($Id));
            $status=$this->Db->query('UPDATE ProteCommunity SET DownVotes=DownVotes+1 WHERE Id=?');
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
        $result=$this->Service->Prote()->DBI()->Community()->VoteMap()->up($this->Service->Auth()->get_uid(),$Id);
        $r=json_decode($result);
        if($r->Status){
            $this->Db->set_parameters(array($Id));
            $status=$this->Db->query('UPDATE ProteCommunity SET UpVotes=UpVotes+1 WHERE Id=?');
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
    	$payload1="CREATE TABLE `ProteCommunity` ( `Id` INT(255) NOT NULL AUTO_INCREMENT , `Name` VARCHAR(255) NOT NULL , `Title` TEXT NOT NULL , `Description` TEXT NOT NULL , `Sidebar` TEXT NOT NULL , `UpVote` INT(255) NOT NULL , `DownVote` INT(255) NOT NULL , `Created` TIMESTAMP NOT NULL , `Modified` TIMESTAMP NOT NULL , `Type` VARCHAR(255) NOT NULL , `Parent` INT(255) NOT NULL , PRIMARY KEY (`Id`) ) ENGINE = InnoDB;";

		$payload2="ALTER TABLE `ProteCommunity` ADD CONSTRAINT `CommunityParent` FOREIGN KEY (`Parent`) REFERENCES `habba.dev`.`ProteCommunity`(`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;";

		$payload3="ALTER TABLE `ProteCommunity` CHANGE `Modified` `Modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';";

        $payload4="ALTER TABLE `ProteCommunity` CHANGE `Created` `Created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
        
       
		$payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
}