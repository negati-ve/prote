<?php 

namespace Etc\AutoLoaders;
use Etc\Config;

class AutoLoad{

    private $FilePath='';
    private $PrependStandard='';
    private $Config;

    public function __construct(Config $Config){
        $this->Config=$Config;
        $this->PrependStandard=realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR )."/Lib/";
    }

    public function standard($className)
    {   
        if($this->Config->debug_mode_is_on()){
            $time=microtime();
        }


        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $this->FilePath=$this->PrependStandard.$className . '.php';
        // echo $this->FilePath."\n";
        if(file_exists($this->FilePath))
            require $this->FilePath;


        if($this->Config->debug_mode_is_on()){
            $this->Config->collect_debug_data($className." Loaded in : ");
            $this->Config->collect_debug_data(microtime()-$time."<br>");
            $this->Config->collect_debug_data("From Location: ".$this->FilePath."<br>");
        }
    }

}
?>