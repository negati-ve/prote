<?php
namespace Prote\DBI\People;
use DIC\Service;
class ProtePeopleContainer {
	private $Storage=array();
	private $Service;

	public function __construct(Service $Service){
		$this->Service=$Service;
	}

	public function available($class){
		if(isset($this->Storage[$class]))
			return true;
		else
			return false;
	}

	public function load($name,$path){
		if($this->available($name))
			return $this->Storage[$name];
		else
			return $this->Storage[$name]=new $path($this->Service);
	}

public function aplauc(){
		return $this->load('Apl','\Prote\DBI\People\aplauc');
	}

	public function Main(){
		return $this->load('Main','\Prote\DBI\People\Main');
	}

	public function ResetMap(){
		return $this->load('ResetMap','\Prote\DBI\People\ResetMap');
	}

	public function Meta(){
		return $this->load('Meta','\Prote\DBI\People\Meta');
	}

	public function Group(){
		return $this->load('Group','\Prote\DBI\People\Group');
	}

	public function GroupMap(){
		return $this->load('GroupMap','\Prote\DBI\People\GroupMap');
	}

	public function Phone(){
		return $this->load('Phone','\Prote\DBI\People\Phone');
	}

	public function PhoneMap(){
		return $this->load('PhoneMap','\Prote\DBI\People\PhoneMap');
	}

	public function InvitePassMap(){
		return $this->load('InvitePassMap','\Prote\DBI\People\InvitePassMap');
	}

	public function College(){
		return $this->load('College','\Prote\DBI\People\College');
	}
	
	public function CollegeBranch(){
		return $this->load('CollegeBranch','\Prote\DBI\People\CollegeBranch');
	}

	public function CollegeMap(){
		return $this->load('CollegeMap','\Prote\DBI\People\CollegeMap');
	}

	public function VerificationMap(){
		return $this->load('VerificationMap','\Prote\DBI\People\VerificationMap');
	}
	
	public function ProfilePictureMap(){
		return $this->load('ProfilePictureMap','\Prote\DBI\People\ProfilePictureMap');
	}

	public function install(){
		$this->Main()->install();
		$this->ResetMap()->install();
		$this->Meta()->install();
		$this->Group()->install();
		$this->GroupMap()->install();
		$this->Phone()->install();
		$this->PhoneMap()->install();
		$this->InvitePassMap()->install();
		$this->College()->install();
		$this->CollegeBranch()->install();
		$this->CollegeMap()->install();
		$this->VerificationMap()->install();
		$this->ProfilePictureMap()->install();
	}
}