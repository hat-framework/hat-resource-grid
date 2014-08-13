<?php

require_once(dirname(__FILE__) . "/classes/grid.php");
class gridResource extends \classes\Interfaces\resource implements grid{

    public    $file_sample = 'sample.php';
    protected $dir  = "";
    protected $type = "Grid";
    private static $instance;

    public function __construct(){
        $this->dir = dirname(__FILE__);
        $this->LoadJsPlugin("grid/jqgrid2", 'obj');
        //$this->LoadJsPlugin("grid/jqgrid", 'obj');
        parent::__contruct();
    }

    public static function getInstanceOf(){
        $class_name = __CLASS__;
        if (!isset(self::$instance)) {
            self::$instance = new $class_name();
        }
        return self::$instance;
    }

    public function load(){
        return $this->obj->load();
    }

    public function configure($titulo, $model, $query = ""){
        return $this->obj->configure($titulo, $model, $query);
    }

    public function draw(){
        return $this->obj->draw();
    }

    public function execute($titulo, $model, $query = "", $gridarr = array()){
        $this->obj->execute($titulo, $model, $query, $gridarr);
    }
    
    public function setEditFormIntegration($funct){
        $this->obj->setEditFormIntegration($funct);
    }
    
    public function setGridOptions($arg){
        $this->obj->setGridOptions($arg);
    }

}