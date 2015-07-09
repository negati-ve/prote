<?php
namespace Prote\DBI\People;
use DIC\Service;

class Phone {
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($number,$service=''){
        $this->Db->set_parameters(array($number,$service));
        return $this->Db->Insert('INSERT INTO ProtePeoplePhone(Number,Service) VALUES(?,?);');
    }

    public function exists($number){
    	$this->Db->set_parameters(array($number));
    	if($this->Db->find_one('SELECT count(*) as C from ProtePeoplePhone WHERE Number=?')->C==1){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeoplePhone WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeoplePhone` (
                    `Id` int(255) NOT NULL,
                      `Number` varchar(20) NOT NULL,
                      `Service` int(255) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $payload2="ALTER TABLE `ProtePeoplePhone`
                    ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Number` (`Number`);
                    ";

        $payload3="ALTER TABLE `ProtePeoplePhone`
                    MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT;";

        $payloads=(array($payload1,$payload2,$payload3));
        $this->Db->drop_payload($payloads,$this);
    }

}