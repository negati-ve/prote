<?php
namespace Database\Blueprints;
use PDO;
abstract class Database implements \Iterator{

	protected $Link=NULL;
    protected $OldLink=NULL;
    protected $TotalLink=0;
    protected $DatabaseHost;
    protected $DatabasePort;
    protected $DatabaseUser;
    protected $DatabasePass;
    protected $DatabaseName;
    protected $Dsn;
    protected $CurrentLink;
    protected $FetchedData;
    protected $query=NULL;
    protected $current=NULL,$next,$key,$valid,$rewind;
    public $parameters=array();
    protected $Config=NULL;
    public $mode=1;

    public function __construct(){
        // $this->DatabaseHost=0;
        $this->DatabaseHost=$this->Config->get_database_host();
        $this->DatabasePort=$this->Config->get_database_port();
        $this->DatabaseName=$this->Config->get_database_name();
        $this->DatabaseUser=$this->Config->get_database_user();
        $this->DatabasePass=$this->Config->get_database_pass();
    }

    /**
     * Connects to Database and gets a handle 
     * @return database handle
     */
	public function connect(){
        if($this->Link!=NULL){
            return $this->Link;
        }
        $this->Dsn = "mysql:host=$this->DatabaseHost;port=$this->DatabasePort();dbname=$this->DatabaseName";
        // $this->TotalLink++;
        try{
            $this->Link=new PDO($this->Dsn,$this->DatabaseUser,$this->DatabasePass,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,PDO::ATTR_PERSISTENT => true,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        }
        catch(\PDOException $e)
        {
            // echo "<center><b><font size='4' color='red'>ERROR: Could not connect to database.</font></b></center>";
            // echo $e->getMessage();
            // $this->TotalLink--;
            // exit();
            return false;
        }   
        // $this->CurrentLink=$this->TotalLink;
        // echo $this->TotalLink;
        return true;
    }

    public function open_new_connection(){
        $this->OldLink=$this->Link;
        $this->Dsn = "mysql:host=$this->DatabaseHost;port=$this->DatabasePort();dbname=$this->DatabaseName";
        try{
            $this->Link=new PDO($this->Dsn,$this->DatabaseUser,$this->DatabasePass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        }
        catch(Exception $e)
        {
            return false;
        }   
        return true;
    }

    public function restore_last_connection(){
        $this->Link=$this->OldLink;
    }

    public function set_parameters($params){
        $this->parameters=$params;
    }

    public function get_parameters(){
        return $this->parameters;
    }

    public function reset_parameters(){
        $this->parameters=array();
    }

    public function set_current_link($current_link){
        $this->CurrentLink=$current_link;
    }

    public function get_current_link(){
        return $this->CurrentLink;
    }

    public function query($d){
        if(!$this->Link){
            $this->connect();
        }
        elseif($this->query){
            $this->query->closeCursor();
        }
        $this->query=$this->Link->prepare($d);
        $this->bind_parameters();
        try{
            $result=$this->query->execute();
        }
        catch(\PDOException $e){
            if($this->Config->debug_mode_is_on()){
                $this->Config->collect_debug_data($e);
                $debug=$this->Config->get_debug_data();
                echo "DATABASE 111 DEBUG";
                var_dump($debug);
                // return $this->query->errorCode();
            }
            return 0;
        }
        return $result;
    }

    // public function

    public function insert($d){

        if(!$this->Link){
            $this->connect();
            if(!$this->Link){

            }
        }
        elseif($this->query){
            $this->query->closeCursor();
        }
        $query=$this->Link->prepare($d);
        for($i=0;$i<count($this->parameters);$i++){
        $query->bindParam($i+1,$this->parameters[$i]);
        }
        try{
            $query->execute();
        }
        catch(\PDOException $e){
             if($this->Config->debug_mode_is_on()){
                $this->Config->collect_debug_data($e->getMessage());
            }
           return false;
        }
         return $this->Link->lastInsertId();
    }


    public function count($table){

        $query=$this->Link->prepare('Select count(*) FROM '.$table);
        $query->execute();
        $query->bindColumn(1, $count);
        $result=$query->fetch(PDO::FETCH_BOUND);
        return $count;

    }

    public function find_one($d){
        if(!$this->Link){
            $this->connect();
        }
        elseif($this->query){
            $this->query->closeCursor();
        }
        $this->query=$this->Link->prepare($d);
        for($i=0;$i<count($this->parameters);$i++){
        $this->query->bindParam($i+1,$this->parameters[$i]);
        }
        try{
            $this->query->execute();
        }
        catch(\PDOException $e){
             if($this->Config->debug_mode_is_on()){
                $this->Config->collect_debug_data($e->getMessage());
            }
            return false;
        }
        $this->current=$this->query->fetch(PDO::FETCH_OBJ);
        return $this->current; 
    }

    public function bind_parameters(){
        for($i=0;$i<count($this->parameters);$i++){
            if(is_int($this->parameters[$i]))
                $this->query->bindParam($i+1,$this->parameters[$i],PDO::PARAM_INT);
            else
                $this->query->bindParam($i+1,$this->parameters[$i]);
        }
    }   
     
    public function find_paired($d){
        if(!$this->Link){
            $this->connect();
        }
        elseif($this->query){
            $this->query->closeCursor();
        }
         $this->query=$this->Link->prepare($d);
        for($i=0;$i<count($this->parameters);$i++){
            if(is_int($this->parameters[$i]))
                $this->query->bindParam($i+1,$this->parameters[$i],PDO::PARAM_INT);
            else
                $this->query->bindParam($i+1,$this->parameters[$i]);
        }
        try{
            $this->query->execute();
        }
        catch(\PDOException $e){
             if($this->Config->debug_mode_is_on()){
                $this->Config->collect_debug_data($e->getMessage());
            }
            return false;
        }
        return $this->query->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function find_many($d){
        if(!$this->Link){
            $this->connect();
        }
        elseif($this->query){
            $this->query->closeCursor();
        }
        $this->query=$this->Link->prepare($d);
        for($i=0;$i<count($this->parameters);$i++){
            // var_dump($this->parameters[$i]);
            if(is_int($this->parameters[$i]))
                $this->query->bindParam($i+1,$this->parameters[$i],PDO::PARAM_INT);
            else
                $this->query->bindParam($i+1,$this->parameters[$i]);
        }
        try{
            $this->query->execute();
        }
        catch(\PDOException $e){
             if($this->Config->debug_mode_is_on()){
                $this->Config->collect_debug_data($e->getMessage());
            }
            return false;
        }
        if($this->mode){
            $this->current=$this->query->fetchAll(PDO::FETCH_OBJ);
            return $this->current;
        }else{
            $this->current=$this->query->fetchAll(PDO::FETCH_ASSOC);
            return $this->current;
        }
    }

    public function page($d){
        $this->query=$this->Link->prepare($d);
        for($i=0;$i<count($this->parameters);$i++){
            if(is_int($this->parameters[$i]))
                $this->query->bindParam($i+1,$this->parameters[$i],PDO::PARAM_INT);
            else
                $this->query->bindParam($i+1,$this->parameters[$i]);
        }
        $this->query->execute();
       return $this->current=$this->query->fetchAll(PDO::FETCH_OBJ);
       // return $result;
    }

    public function fall(){
        $this->current=$this->query->fetchAll(PDO::FETCH_OBJ);
        return $this->current;
    }

    public function current(){
        if ($this->current!=NULL)
            return $this->current;
        $this->current=$this->query->fetch(PDO::FETCH_OBJ);
    }

    public function next(){
        $this->current=$this->query->fetch(PDO::FETCH_OBJ);
        return $this->current;
    }

    public function key(){
    }

    public function valid(){
    }

    public function rewind(){
    }

    public function last(){
        return $this->Link->lastInsertId();
    }

    public function __destruct(){
        // $this->Link->closeCursor();
        // unset($this->Link);
        $this->Link = NULL;

    }
}
?>