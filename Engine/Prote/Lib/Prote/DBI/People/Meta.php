<?php
namespace Prote\DBI\People;
use DIC\Service;

class Meta {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }
    public function get_all_by_id($uid){
        return $this->get_all_by_uid($uid);
    }
    public function get_all_by_uid($uid){
        $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('Select pp.Id,pp.Email,pp.Handle,ppm.*,ppcm.Usn,ppc.Name as College, ppcb.Name as Branch,ppp.Number as Phone, ppgm.GroupId as Gid, ppg.Name as Gname FROM
                                        ProtePeople pp
                                        LEFT JOIN 
                                        ProtePeopleMeta ppm ON pp.Id=ppm.Uid
                                        LEFT JOIN 
                                        ProtePeopleCollegeMap ppcm on ppm.Uid=ppcm.Uid
                                        LEFT JOIN
                                        ProtePeopleCollegeBranch ppcb on ppcb.Id=ppcm.BranchId
                                        LEFT JOIN
                                        ProtePeopleCollege ppc on ppc.Id=ppcm.CollegeId
                                        LEFT JOIN
                                        ProtePeopleGroupMap ppgm on ppm.Uid=ppgm.Uid
                                        LEFT JOIN
                                        ProtePeopleGroup ppg on ppg.Id=ppgm.GroupId
                                        LEFT JOIN
                                        ProtePeoplePhoneMap pppm on pppm.Uid=ppm.Uid
                                        LEFT JOIN
                                        ProtePeoplePhone ppp on ppp.Id=pppm.PhoneId
                                        where ppm.Uid=?')){
            return $data;
        }
        else{
            return 0;
        }
    }
    
    public function exists($uid){
    	$this->Db->set_parameters(array($uid));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeopleMeta WHERE Uid=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function add($uid,$first_name,$last_name,$dob,$gender,$inviter,$story){
        $this->Db->set_parameters(array($uid,$first_name,$last_name,$dob,$gender,$inviter,$story));
        return $this->Db->Insert('INSERT INTO ProtePeopleMeta(Uid,FirstName,LastName,DOB,Gender,InviterId,Story) VALUES(?,?,?,?,?,?,?);');
    }


    public function get_first_name($uid){
		$this->Db->set_parameters(array($uid));
    	if($data=$this->Db->find_one('SELECT FirstName from ProtePeopleMeta where Uid=?')){
    		return $data->FirstName;
    	}else{
    		return 0;
    	}
    }

    public function get_last_name($uid){
        $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT LastName from ProtePeopleMeta where Uid=?')){
            return $data->LastName;
        }else{
            return 0;
        }
    }

    // public function get_uid_by_first_name($name){
    //     $this->Db->set_parameters(array('%'.$name.'%'));
    //     if($data=$this->Db->find_one('SELECT Uid from ProtePeopleMeta where FirstName Like ?')){
    //         return $data->Uid;
    //     }else{
    //         return 0;
    //     }
    // }

    public function get_user_for_autocomplete($name){
        $this->Db->set_parameters(array('%'.$name.'%'));
        if($data=$this->Db->find_many('SELECT Uid,concat(FirstName," ",LastName) as Name from ProtePeopleMeta where FirstName Like ?')){
            return $data;
        }else{
            return 0;
        }
    }

    public function get_dob($uid){
        $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT DOB from ProtePeopleMeta where Uid=?')){
            return $data->DOB;
        }else{
            return 0;
        }
    }

    public function get_gender($uid){
        $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT Gender from ProtePeopleMeta where Uid=?')){
            return $data->Gender;
        }else{
            return 0;
        }
    }

    public function get_inviter($uid){
         $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT InviterId from ProtePeopleMeta where Uid=?')){
            return $data->InviterId;
        }else{
            return 0;
        }

    }

    public function get_story($uid){
         $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT Story from ProtePeopleMeta where Uid=?')){
            return $data->Story;
        }else{
            return 0;
        }

    }

    public function get_created($uid){
        $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT Created from ProtePeopleMeta where Uid=?')){
            return $data->Created;
        }else{
            return 0;
        }
    }

    public function get_last_seen($uid){
         $this->Db->set_parameters(array($uid));
        if($data=$this->Db->find_one('SELECT LastSeen from ProtePeopleMeta where Uid=?')){
            return $data->LastSeen;
        }else{
            return 0;
        }
    }

    public function update_first_name($uid,$first_name){
         $this->Db->set_parameters(array($first_name,$uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET FirstName=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function update_last_name($uid,$last_name){
         $this->Db->set_parameters(array($last_name,$uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET LastName=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function update_dob($uid,$dob){
         $this->Db->set_parameters(array($dob,$uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET DOB=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function update_gender($uid,$gender){
         $this->Db->set_parameters(array($gender,$uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET Gender=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function update_inviter($uid,$inviter){
         $this->Db->set_parameters(array($inviter,$uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET InviterId=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function update_last_seen($uid){
        $this->Db->set_parameters(array($uid));
        if($this->Db->query('UPDATE ProtePeopleMeta SET LastSeen='));
    }

    public function install(){
    	$payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleMeta` (
                  `Uid` int(255) NOT NULL,
                  `FirstName` varchar(255) NOT NULL,
                  `LastName` varchar(255) NOT NULL,
                  `DOB` date NOT NULL,
                  `Gender` varchar(1) NOT NULL,
                  `InviterId` int(255) NOT NULL,
                  `Story` text NOT NULL,
                  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `LastSeen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `Logins` int(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		
        $payload2="ALTER TABLE `ProtePeopleMeta`
                    ADD UNIQUE KEY `Uid_2` (`Uid`), ADD KEY `Uid` (`Uid`);";

		$payload3="ALTER TABLE `ProtePeopleMeta`
                    ADD CONSTRAINT `MetaUid` FOREIGN KEY (`Uid`) REFERENCES `ProtePeople` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		
        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }
}