<?php
namespace Prote\DBI\People;
use DIC\Service;
class CollegeBranch{
    private $Service=NULL;
    public $Db=NULL;

    public function __construct(Service $Service){
        $this->Service=$Service;
        $this->Db=$this->Service->Database();
    }

    public function add($name){
        $this->Db->set_parameters(array($name));
        return $this->Db->insert('INSERT INTO ProtePeopleCollegeBranch(Name) VALUES(?);');
    }

    public function exists($name){
    	$this->Db->set_parameters(array($name));
    	if($Id=$this->Db->find_one('SELECT Id as C from ProtePeopleCollegeBranch WHERE Name=?')->C){
    		return $Id;
    	}
    	else{
    		return 0;
    	}
    }

    public function remove($id){
        $this->Db->set_parameters(array($id));
        if($this->Db->query('DELETE FROM ProtePeopleCollegeBranch WHERE Id=?')){
            return 1;
        }else{
            return 0;
        }
    }

    public function install(){
        $payload1="CREATE TABLE IF NOT EXISTS `ProtePeopleCollegeBranch` (
                  `Id` int(255) NOT NULL,
                  `Name` varchar(255) NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
                    ";

        $payload2="INSERT INTO `ProtePeopleCollegeBranch` (`Id`, `Name`) VALUES
                    (1, 'Acharya College of Education'),
                    (2, 'BE - Manufacturing Science & Engg'),
                    (3, 'B.E. - Aeronautical'),
                    (4, 'B.E. - Bio Technology'),
                    (5, 'B. B. M - Bachelor of Business Management Arts & Science'),
                    (6, 'M.Tech - Digital Communication Engineering'),
                    (7, 'Diploma in Engg. -Mechanical Engineering'),
                    (8, 'B.E. - Automobile'),
                    (9, 'B.E. - Electrical & Electronics'),
                    (10, 'B.E. - Computer Science'),
                    (11, 'M. B. A - Master of Business Administration'),
                    (12, 'B.E. - Information Science'),
                    (13, 'B.E. - Civil Engineering'),
                    (14, 'B.E. - Mechatronics'),
                    (15, 'B.E. - Mechanical Engineering'),
                    (16, 'M. F. A - Master of Finance & Accounting'),
                    (17, 'M. C. A - Master of Computer Application'),
                    (18, 'Diploma in Engg. - Aeronautical Engineering'),
                    (19, 'Diploma in Engg. - Mechatronics'),
                    (20, 'B. C. A - Bachelor of Computer Application'),
                    (21, 'B.E. - Electronics & Communication'),
                    (22, 'P.G.D.M. - Post Graduate Diploma in Management'),
                    (23, 'B. Sc. F. A. D - Bachelor of Science (Fashion & Apparel Design)'),
                    (24, 'Diploma in Engg. - Electronics & Communication'),
                    (25, 'Bachelor of Architecture'),
                    (26, 'B.Com'),
                    (27, 'B. A - Bachelor of Arts (Journalism / Psychology)'),
                    (28, 'M.Com'),
                    (29, 'Science - Physics, Chemistry, Maths, Biology (PCMB)'),
                    (30, 'Diploma in Engg. - Computer Science'),
                    (31, 'B.E. - Construction Technology & Management'),
                    (32, 'BE - Mining'),
                    (33, 'M.Tech - Power Systems'),
                    (34, 'B. Pharm - Bachelor of Pharmacy'),
                    (35, 'Pharm D - Doctor of Pharmacy'),
                    (36, 'M. S - Mass Communication'),
                    (38, 'Unknown'),
                    (39, 'M. Pharm - Master of Pharmacy'),
                    (40, 'Diploma in Architecture'),
                    (41, 'Diploma in Engg. - Electrical & Electronics'),
                    (42, 'B. A - Bachelor of Arts (Journalism)'),
                    (43, 'Science - Physics, Chemistry, Maths, Computer Science (PCMC)'),
                    (44, 'Diploma in Engg. - Civil Engineering'),
                    (45, 'Sports'),
                    (46, 'M.Tech - Production Design & Manufacturing'),
                    (47, 'M.Tech - Computer Networking'),
                    (48, 'Housekeeping'),
                    (49, 'Commerce - Computer Science, Economics, Business Studies, Accountancy (CEBA)'),
                    (50, 'Diploma in Commercial Practice'),
                    (51, 'Admin'),
                    (52, 'M.Tech - Computer Science & Engineering'),
                    (53, 'D. Pharm - Diploma in Pharmacy'),
                    (54, 'Diploma in Engg. - Automobile Engineering'),
                    (55, 'M. I. B - Master of International Business'),
                    (56, 'M. Sc. - Chemistry'),
                    (57, 'Library');";
        $payload3="ALTER TABLE `ProtePeopleCollegeBranch`
                    ADD PRIMARY KEY (`Id`);";
        $payload4="ALTER TABLE `ProtePeopleCollegeBranch`
                    MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=58;";
        $payloads=(array($payload1,$payload2,$payload3,$payload4));
        $this->Db->drop_payload($payloads,$this);
    }
    
}