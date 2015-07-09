<?php
namespace Database\Blueprints;
use PDO;
abstract class pdochain{
    private $fields,$where,$table,$order,$group,$limit,$type,$having,$link,$query,$page;
    public function __construct(){
    }
    public function query($query = null){
        new main();
        switch($this->type){
            case 'insert':
                $this->query = 'insert into
                    '.$this->table.' 
                    ('.implode(',',$this->fields['names']).')
                    values 
                    ('.implode(',',$this->fields['values']).')';
            break;
            case 'update':
                $this->query = 'update
                    '.$this->table.' 
                    set    
                    '.(!is_null($this->fields)?$this->fields:null).'
                    '.(!is_null($this->where)?"where {$this->where}":null);
            break;
            case 'delete':
                $this->query = 'delete from 
                    '.$this->table.' 
                    '.(!is_null($this->where)?"where {$this->where}":null);
            break;
            case 'select':
                $this->query = 'select 
                    '.(is_null($this->fields)?'*':$this->fields).'
                    from '.$this->table.' 
                    '.(!is_null($this->where)?"where {$this->where}":null).'
                    '.(!is_null($this->order)?"order by {$this->order}":null).'
                    '.(!is_null($this->group)?"group by {$this->group}":null).'
                    '.(!is_null($this->limit)?"limit {$this->limit}":null).'
                    '.(!is_null($this->page)?", {$this->page}":null).'
                    '.(!is_null($this->having)?"having {$this->having}":null);
            break;
            default:
                if(!is_null($query))
                     $this->query = $query;   
        }
  $this -> query = str_replace('##p##',$this->conf()->database->prefix,$this -> query);
    if(isset($_GET['debug']) || $this -> conf() -> debug)
        echo "<pre>{$this->query}</pre>";
        $dsn = "{$this -> conf() -> database -> type}:dbname={$this -> conf() -> database -> name};host={$this -> conf() -> database -> server}";
        try {
            $pdo = new PDO($dsn, $this -> conf() -> database -> user, $this -> conf() -> database -> pwd);
            $cursor = $pdo->prepare($this->query);
            if(!$cursor->execute())
                return false;
            if($this -> type == 'insert')
                $_SESSION['last_id'] = $pdo -> lastInsertId();
        unset($this -> fields,$this -> where,$this -> table,$this -> order,$this -> group,$this -> limit,$this -> type,$this -> having,$this -> link,$this -> query,$this -> page);
            return $cursor->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    private function setDataFields($data){
        switch($this->type){
            case 'insert':
                foreach($data as $name => $value){
                    $names[] = $name;
                    $values[] = $this -> setValueType($value, $name);
                }
                return array('names'=>$names,'values'=>$values);
            break;
            case 'update':
                foreach($data as $name => $value)
                    $arr[] = "$name = {$this -> setValueType($value, $name)}";
                return implode(',',$arr);
            break;
            default:
                return implode(',',$data);
        }
    }
    private function setValueType($value, $name){
    switch($name){
        case 'updated_at':
        case 'created_at':
        case 'value':
        case 'current_value':
        return $value;
        break;
        default:
        return "'$value'";
    }
    }
    public function type($data){
        $this -> type = $data;
        return $this;
    }
    public function where($data){
         $this -> where = $data;   
         return $this;
    }
    public function having($data){
         $this -> having = $data;   
         return $this;
    }
    public function fields($data = null){
        $this -> fields = $this->setDataFields($data);
        return $this;
    }
    public function table($data){
        $this -> table = $data;
        return $this;
    }
    public function order($data){
        $this -> order = $data;
        return $this;
    }
    public function group($data){
        $this -> group = $data;
        return $this;
    }
    public function limit($data){
        $this -> limit = $data;
        return $this;
    }
    public function page($data = null){
        if(!is_null($data))
            $this -> limit = $data;
        else
            $this -> limit = $_GET['p'];
        return $this;
    }
}
