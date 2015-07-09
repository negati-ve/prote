<?php
namespace Database;
use Database\Blueprints\Database as Database_Blueprint,
DIC\Service;
class VADB extends Database_Blueprint{

	private $Stats=NULL;
	private $paginating=NULL;
	public function __construct(Service $Service){
		$this->Service=$Service;
		$this->Config=$this->Service->Config();
		parent::__construct();
		$this->connect();
		
	}
	public function load_stats(){
		$Day=time()-60*60*24;

		$this->set_parameters(array($Day,$Day,(time()-60*15)));
		$this->Stats=$this->find_many('
			SELECT
			count(*) AS total_users,
			SUM(created > ?) AS registrations_today,
			SUM(last_seen > ?) AS returning_users,
			SUM(last_seen > ?) AS users_online
			FROM People
			');
		$this->get_total_users();
		$this->get_returning_users();
		$this->get_registrations_today();
		$this->get_users_online();
	}

	public function get_users_online(){
		return $this->Service->Html()->users_online=$this->Stats->users_online;
	}

	public function get_total_users(){
		return $this->Service->Html()->total_users=$this->Stats->total_users;
	}

	public function get_returning_users(){
		return $this->Service->Html()->returning_users=$this->Stats->returning_users;
	}

	public function get_registrations_today(){
		return $this->Service->Html()->registrations_today=$this->Stats->registrations_today;
	}

	public function get_notes($uid=NULL){
		if($uid){
			$this->set_parameters(array($uid));
			$notes=$this->find_many("Select note,timestamp from AdminNotes Where uid=?");
		}
		else
			$notes=$this->find_many("Select note,timestamp from AdminNotes");
		return $notes;
	}


	public function paginate_notes($start,$iterations,$uid=NULL){
		// if($this->paginating){
		// 	// yield $this->next();
		// }
		// else{
			if($uid){
				$this->set_parameters(array($uid,$start,$iterations));
				$notes=$this->page("Select note,timestamp from AdminNotes Where uid=? LIMIT ?,?");
				$this->paginating=true;
				return $this->current();
				// yield $this->next();
			}
			else{
				// $this->Link->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	            // echo $this->parameters[$i];
				$this->set_parameters(array($start,$iterations));
				$notes=$this->page("Select note,timestamp,first_name,name as college from AdminNotes notes LEFT JOIN People user ON notes.uid=user.id LEFT JOIN college clg ON user.college=clg.id LIMIT ?,?");
				return $this->current;
				// exit;
				// $this->paginating=true;
				// yield $this->next();
			}
		// }
	}


	public function create_note($uid,$text){
		$this->set_parameters(array($uid,$text));
		$notes=$this->insert("insert into AdminNotes set uid=?, note=?");
	}



}

?>