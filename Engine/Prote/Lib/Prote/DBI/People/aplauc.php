<?php
namespace Prote\DBI\People;
use DIC\Service;

class aplauc {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function exists($uid){
    	$this->Db->set_parameters(array($uid));
    	if($this->Db->find_one('SELECT count(*) as C from apl WHERE Uid=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function add($name,$phone,$clg,$branch,$uid){
       
        $this->Db->set_parameters(array($name,$phone,$clg,$branch,$uid));
        return $this->Db->Insert('INSERT INTO `apl` (`name`, `phone`, `clg`, `branch`, `uid`) VALUES (?,?,?,?,?);');
    }

    public function update_name($uid,$name){
         $this->Db->set_parameters(array($name,$uid));
        if($this->Db->query('UPDATE apl SET FirstName=? where Uid=?')){
            return 1;
        }else{
            return 0;
        }                    
    }
    public function install(){
    	$payload1="CREATE TABLE IF NOT EXISTS `apl` (
                    `name` varchar(25) NOT NULL,
                      `phone` varchar(10) NOT NULL,
                      `clg` varchar(50) NOT NULL,
                      `branch` varchar(30) NOT NULL,
                      `uid` int(2) NOT NULL AUTO_INCREMENT,
                      PRIMARY KEY (`uid`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";
        $payloads=(array($payload1));
        $this->Db->drop_payload($payloads,$this);
    }
}