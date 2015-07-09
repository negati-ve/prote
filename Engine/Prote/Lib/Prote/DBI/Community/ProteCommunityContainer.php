<?php
namespace Prote\DBI\Community;
use DIC\Service;
class ProteCommunityContainer {
	private $Storage=array();
	private $Service;

	public function __construct(Service $Service){
		$this->Service=$Service;       
		$Service->Html()->CommunityUrlPrefix='Habba15';
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

	public function Main(){
		return $this->load('Main','\Prote\DBI\Community\Main');
	}

	public function AdminMap(){
		return $this->load('AdminMap','\Prote\DBI\Community\AdminMap');
	}

	public function VoteMap(){
		return $this->load('VoteMap','\Prote\DBI\Community\VoteMap');
	}

	public function Post(){
		return $this->load('Post','\Prote\DBI\Community\Post');
	}

	// public function PostVoteMap(){
	// 	return $this->load('PostMap','\Prote\DBI\Community\PostVoteMap');
	// }

	public function PostVoteMap(){
		return $this->load('PostVoteMap','\Prote\DBI\Community\PostVoteMap');
	}

	public function PostComment(){
		return $this->load('PostComment','\Prote\DBI\Community\PostComment');
	}
	
	public function install(){
		$this->Main()->install();
	}
}