<?php

use classes\Classes\JsPlugin;
class jqgrid2Js extends JsPlugin implements grid {
    
    public $project_url = 'http://www.trirand.com/blog/?page_id=5';
    public $file_sample = 'sample.php';
    private $configured = false;
    private $functions  = "";
    private $vars       = "";

    public function init(){
        require_once 'jqgridGrid.php';
        $this->grid = new jqgridGrid($this->url);
        $this->load();
    }
    
    static private $instance;
    public static function getInstanceOf($plugin){
        $class_name = __CLASS__;
        if (!isset(self::$instance)) {
            self::$instance = new $class_name($plugin);
        }

        return self::$instance;
    } 
    
    public function execute($titulo, $model, $query = "", $gridarr = array()){
        $this->grid->execute($titulo, $model, $query, $gridarr);
    }
    
    public function setEditFormIntegration($funct){
        $this->grid->setEditFormIntegration($funct);
    }
    
    public function setGridOptions($funct){
        $this->grid->setGridOptions($funct);
    }

    //carrega os arquivos javascript
    public function load(){

        $url = "$this->url/files";

        //$this->Html->LoadJs("$url/js/ui.multiselect");
        $this->Html->LoadJs("$url/js/i18n/grid.locale-pt-br");
        $this->Html->LoadJs("$url/js/jquery.jqGrid.min");
        
        $this->Html->loadCss("plugins/jqgrid/jqgrid");
        $this->Html->loadCss("plugins/jqueryui/multiselect");
        $this->LoadJsPlugin("jqueryui/jqueryui", "jqui");

        $this->Html->LoadJsFunction("
            $.jgrid.no_legacy_api = true;
            $.jgrid.useJSON = true;
            var lastSelection;
        ");
    }
    
}