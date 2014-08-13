<?php

use classes\Classes\Object;
class jqgridGrid extends classes\Classes\Object{

    private $node           = "";
    private $summaryrows    = NULL;
    private $functions      = array();
    private $options        = array();
    private $gridoptions    = array();
    private $selection      = array();
    private $selection_list = array();
    private $arr            = array();
    private $join           = "";
    private $ExtraQuery     = "";
    private $gridarr        = "";
    private $efintegration  = "";
    public function  __construct($url) {
        $this->url = $url . "/";
        $this->LoadResource('database', 'db');
        $this->LoadResource('html', 'html')->LoadCss('plugins/jqgrid/jqgrid');
        
        require_once 'files/jq-config.php';
    }

    public function execute($titulo, $model, $query = "", $gridarr = array()){
        if($query != ""){
            $_SESSION['jqgrid_query'][$model]['query'] = $query;
            $_SESSION['jqgrid_query'][$model]['garr']  = $gridarr;
        }else{
            if($query == ""     && isset($_SESSION['jqgrid_query'][$model]['query'])) $query   = $_SESSION['jqgrid_query'][$model]['query'];
            if(empty ($gridarr) && isset($_SESSION['jqgrid_query'][$model]['garr']))  $gridarr = $_SESSION['jqgrid_query'][$model]['garr'] ;
        }
        if($titulo == ""){
            $titulo = explode("/", $model);
            $titulo = "Visualizar " .ucfirst(end($titulo));
        }
        $this->gridarr = $gridarr;
        //$this->gridarr = null;
        $this->ExtraQuery = ($query == "")?NULL:"WHERE $query ";
        
        $this->configure($model, $titulo);
        $this->formIntegration($model);
       
        $gridarr = array_values($gridarr);
        ($this->node == "") ?
           $this->grid->renderGrid("#$this->list","#$this->pager" ,true, $this->summaryrows, $gridarr, true, true):
           $this->grid->renderTree("#$this->list", "#$this->pager",true, $this->summaryrows, $gridarr, true, true);
        
        
        $this->LoadResource('html','html')->LoadJqueryFunction("$('.jqgrid-overlay').hide();");
    }
    
    public function addSumary($arr){
        $this->summaryrows = $arr;
    }
    
    private function configure($model, $titulo){
        $this->LoadModel($model, 'model');
        $keys   = array();
        $tabela = $this->model->getTable();
        $dados  = $this->model->getDados();
        $this->initVars();
        $this->detectTreegrid($dados, $tabela);
        $this->LoadGrid();
        $this->configureTypes($tabela, $dados, $keys);
        $this->initgrid($tabela, $this->pkey, $keys, $titulo, $model);
    }
    
        private function initVars(){
            static $i = 0; $i++;
            $this->list  = "list$i";
            $this->pager = "pager$i";
        }

        private function detectTreegrid($arr, $tabela){
            if(array_key_exists("treegrid", $this->options)){return;}
            foreach($arr as $name => $var){
                if(!isset($var['fkey']) || $var['fkey']['cardinalidade'] !== '1n'){continue;}
                if($tabela !== $this->LoadModel($var['fkey']['model'], 'fkmodel')->getTable()){continue;}
                $this->node = $name;
            }
        }

        private function LoadGrid(){
            $path  = !($this->node != "")?"jqGrid"      :"jqTreeGrid";
            $class = !($this->node != "")?"jqGridRender":"jqTreeGrid";
            if(!function_exists("loadgridobj")){
                function loadgridobj($path, $class){
                    require_once ABSPATH."php/jqCalendar.php"; 
                    require_once ABSPATH."php/jqGridPdo.php";
                    require_once ABSPATH."php/jqAutocomplete.php";
                    $conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
                    $conn->query("SET NAMES " . CHARSET);

                    require_once ABSPATH."php/$path.php";
                    return new $class($conn);
                }
            }
            $this->grid = loadgridobj($path, $class);
        }

        private function configureTypes($tabela, $dados, &$keys){
            $pkey = array();
            $nm   = "";
            foreach($dados as $name => $arr){
                $nm = $name;
                if(!array_key_exists("grid", $arr)) {continue;}
                if(array_key_exists("pkey", $arr)) {$pkey[] = $name;}
                if(array_key_exists('fkey', $arr)) {
                    $nm = $this->fkey($name, $arr, $tabela);
                    $arr['size'] = 64;
                }
                if($nm != ""){
                    $keys[] = $nm;
                    if(false !== strstr($nm, ' as ')){
                        $e  = explode(" as ", $nm);
                        $nm = trim(end($e));
                    }
                    if(array_key_exists("name", $arr)) {$this->addOption($nm, "label", $arr['name']);}
                    
                }
                $type = $arr['type'];
                $this->size_format($arr, $type, $nm);
                $this->formatters($name, $type, $arr);
            }

            //seta as variaveis
            $this->pkey = implode(", ", $pkey);
        }
    
        private $formatters = array(
            'int'       => array('formatter' => 'integer' ,'sorttype' => 'integer' ,'align' => 'right', 'formatoptions'=>array('decimalSeparator' => ",", 'thousandsSeparator' => ".")), 
            'decimal'   => array('formatter' => 'currency','sorttype' => 'currency','align' => 'right', 'formatoptions'=>array('decimalSeparator' => ",", 'thousandsSeparator' => "."), 'prefix'=>"R$",), 
            'float'     => array('formatter' => 'number'  ,'sorttype' => 'number'  ,'align' => 'right', 'formatoptions'=>array('decimalSeparator' => ",", 'thousandsSeparator' => ".")), 

            'date'      => array('formatter' => 'date', 'formatoptions' => array('srcformat'=>'Y-m-d'      , 'newformat'=>'d/m/Y')      , 'fn' => 'format_date',
                'editoptions' => array('dataInit' => 'js:function(elm){setTimeout(function(){ 
                        jQuery(elm).datepicker({dateFormat:"yy-mm-dd"}); 
                        jQuery(".ui-datepicker").css({"font-size":"75%"}); 
                    },200);}')),
            'time'      => array('formatter' => 'date', 'formatoptions' => array('srcformat'=>'h:i:s'      , 'newformat'=>'h:i:s')      , 'fn' => 'format_date'),
            'datetime'  => array('formatter' => 'date', 'formatoptions' => array('srcformat'=>'Y-m-d h:i:s', 'newformat'=>'d/m/Y h:i:s'), 'fn' => 'format_date'),
            'timestamp' => array('formatter' => 'date', 'formatoptions' => array('srcformat'=>'Y-m-d h:i:s', 'newformat'=>'d/m/Y h:i:s'), 'fn' => 'format_date'),
            
            'enum'      => array('fn' => 'format_enum'),
            'varchar'   => array('formatter' => 'sharenameFormatter', 'formatoptions' => array('autoencode' => true)),
            'text'      => array('formatter' => 'sharenameFormatter', 'formatoptions' => array('autoencode' => true)),
        );
    
            private function size_format($arr, $type, $nm){
                $sz = 60;
                if(array_key_exists("size", $arr) && array_key_exists('type', $arr)){
                    if($type == 'int' || $type == 'decimal' || $type == 'float'){
                        $sz = explode(",", $arr['size']);
                        $sz = array_shift($sz);
                        $sz = $sz * 4;
                        if($sz >= 60){$sz=60;}
                    }
                }else{$sz = 60;}
                $this->addOption($nm, "width", $sz);
            }

            private function formatters($name, $type, $arr){
                if(!isset($this->formatters[$type])){return;}
                $options = $this->formatters[$type];
                $fn      = '';
                if(isset($options['fn'])){
                    $fn=$options['fn'];
                    unset($options['fn']);
                }
                foreach($options as $key => $value){
                    $this->addOption($name, $key, $value);
                }
                if($fn === '' || !method_exists($this, $fn)){return;}
                $this->$fn($name, $arr,  $this->grid);
            }

                private function format_date($name, $arr, $grid){
                    $this->addOption($name, 'formatoptions', array("srcformat"=>"Y-m-d H:i:s","newformat"=>"d/m/Y H:i:s"));
                    $this->addOption($name, 'editoptions', array("dataInit"=> 
                        "js:function(elm){setTimeout(function(){ 
                            jQuery(elm).datepicker({dateFormat:'dd/mm/yy', timeFormat: 'hh:mm:ss'});
                        },200);}") );
                    $this->addOtherFunction($name, 'setDatepicker', array("buttonIcon"=>true, true, false));
                    $grid->datearray[] = $name; 
                }

                private function format_enum($name, $arr){
                    $arr['options'][] = "Nenhuma Opção";
                    $default          = array("" => "Nenhuma Opção");
                    $this->addSelectionList($name, $arr['options'], $default);
                }
                
    private function addOtherFunction($name, $fnname, $vars){
        if(!isset($this->functions[$name])){$this->functions[$name] = array();}
        $this->functions[$name][] = array('func'=>$fnname, 'vars'=>$vars);
    }
    
    private function initgrid($tabela, $pkey, $keys, $titulo, $model){
        //configurações do grid
        $ks = implode(", ", $keys);
        $this->grid->SelectCommand = "SELECT $ks FROM $tabela $this->join $this->ExtraQuery";
        $this->grid->table = $tabela;
        $this->grid->setPrimaryKeyId($pkey);  
        
        $this->grid->setColModel(null, array_values($this->gridarr));
        $this->grid->setColProperty($pkey, array("editoptions"=>array("readonly"=>true)));
        $this->renderOptions();
        
        $this->grid->dataType      = 'json';
        $this->grid->cacheCount    = false;
        $this->grid->inlineNav     = false;
        $this->grid->navigator     = true;
        $this->grid->showError     = true;
        $this->grid->toolbarfilter = true; 
        
        $this->registerOption();
        $this->grid->setGridOptions(array("altRows"   => true,"caption" => "$titulo", "hoverrows"=> true, "multiselect" => true)); 
        $this->grid->setGridOptions(array("autowidth" => true,"height"  => 'auto')); 
        $this->grid->setGridOptions(array("rowList"   => array(10,20,50,100,200), "rowNum" => 10));
        $this->grid->setGridOptions(array('sortable'  => true, "sortorder" => "DESC", "sortname" => "$this->pkey"));
        $this->grid->setGridOptions(array("editurl"   => $this->url."action.php?model=$model","url"=> $this->url."action.php?model=$model")); 
        $this->grid->setGridOptions(array("postData" => $this->gridarr));
        
        if($this->node != ""){
            $this->grid->setTreeModel('adjacency');
            $this->grid->setTableConfig(array('id'=> $pkey, 'parent'=> $this->node));
            $this->grid->autoLoadNodes = false; 
            $this->grid->expandAll     = true;
            $this->grid->setColProperty($this->node ,array("hidden"=>true));
            //$this->grid->setColProperty($this->pkey ,array("hidden"=>true));
            //$this->grid->callGridMethod("#$this->list", 'bindKeys');
        }else{
            //Número as linhas
            $this->grid->setGridOptions(array("rownumbers" => true, "rownumWidth" => 35));
            $this->registerOption();
        }   
        
        if(empty ($this->gridoptions)) {$this->setGridOptions(array());}
        $this->grid->setJSCode(
        "jQuery('#searchtoolbar').click(function(){ 
            jQuery('#$this->list')[0].triggerToolbar(); 
            return false; 
        });"); 
        
        $this->grid->setNavOptions('navigator', $this->gridoptions);
        // prevent some executions when not excel export 
        $oper = jqGridUtils::GetParam("oper"); 
        if($oper == "pdf") { 
            $this->grid->setPdfOptions(array(
                "header"=>true, 
                "margin_top"=>27, 
                //"header_logo"=>"logo.gif", 
                //"header_logo_width"=>30, 
                "header_title"=> $titulo, 
                "header_string"=>"Exportação de dados do site ".SITE_NOME 
             )); 
        }
    }
    
    public function setGridOptions($arr){
        $opt = array();
        $avaible = array("add", "edit","del","view", "search", "excel","pdf", "csv", "columns");
        $arr = (empty ($arr))? array("del", "search", "excel","pdf", "csv", "columns"):$arr ;
        //$arr = $avaible;
        foreach($avaible as $av){
            if(in_array($av, $arr)) $opt[$av] = true;
            else $opt[$av] = false;
        }
        $this->gridoptions = $opt;
    }
    
    private function fkey($name, &$arr, $tabela){
        $fk      = $arr['fkey'];
        $card    = $fk['cardinalidade'];
        $fkmodel = $fk['model'];
        $keys    = $fk['keys'];
        $k1      = $keys[0];
        $k2      = $keys[1];

        $this->LoadModel($fkmodel, 'fkmodel');
        $tb2 = $this->fkmodel->getTable();
        if($card == "1n"){
            if($tb2 == $tabela){
                if(!array_key_exists("treegrid", $this->options)){
                    $this->node = $name;
                    $exp = array_key_exists(1, $keys)?$keys[1]:$name;
                    $this->setOptions(array('ExpandColumn' => $exp));
                }
                return $name;
            }
            
            $tempt = $this->AddJoin($tb2, $name, $k1, $tabela);
            $this->addSelection($name, array(
                'url' => $fkmodel,
                'query' => "SELECT $tb2.$k2 as $name, $tb2.$k1 FROM $tb2 WHERE $tb2.$k2 ORDER BY $tb2.$k2",
            ));
            //return "$tempt.$k2 as $name"; 
            return "$tempt.$k2";//tem bug aqui.. mas, já cansei de mexer nisto.
        }
        elseif($card == "11"){
            $dados = $this->fkmodel->getDados();
            $this->AddJoin($tb2, $name, $k1, $tabela);
            foreach($dados as $nms => $a){
                if(array_key_exists("grid", $a)){
                    $arr[$nms] = $a;
                }
            }
        }
        
    }
    
     private function registerOption(){
        foreach($this->options as $arr) {$this->grid->setGridOptions($arr); }
        $this->options = array();
        foreach($this->selection as $name => $query){
            $request = array();
            if(is_array($query) && isset($query['url'])){
                //$request['url'] = $query['url'];
                $this->grid->setUrl($this->url."action.php");
                $query = $query['query'];
            }
            //$this->grid->setSelect($name, $query, true, true, true, array("" => "Nenhum"));
            $this->grid->setAutocomplete($name,"#$name",$query,$request,true,true);
        }
        
        foreach($this->selection_list as $name => $array){
            $this->grid->setSelect($name, $array['options'], true, true, true, $array['default']);
        }
        
        foreach($this->functions as $name => $function){
            foreach($function as $options){
                $method_name = $options['func'];
                if(!method_exists($this->grid, $method_name)){die("O método $method_name não existe na classe grid!");}
                call_user_func_array(array($this->grid, $method_name), $options['vars']);
            }
        }
        
        $this->functions      = array();
        $this->selection_list = array();
        $this->selection      = array();
    }
    
    private function addSelection($name, $query){
        $this->selection[$name] = $query;
    }
    
    private function addSelectionList($name, $options, $default){
        $this->selection_list[$name] = array('options' => $options, 'default' => $default);
    }
    
    private function AddJoin($tabela2, $k1src, $k2src, $tabela){
        static $joins = array();
        if(!isset($joins[$tabela2])){$joins[$tabela2] = 0;}
        $joins[$tabela2]++; $kjoin = $tabela2.'_'.$joins[$tabela2];
        if($k1src == $k2src)$this->join .= " NATURAL JOIN $tabela2 ";
        else $this->join .= " LEFT JOIN $tabela2 as $kjoin ON $tabela.$k1src = $kjoin.$k2src ";
        return $kjoin;
    }
    
    private function setOptions($options){
        if(!is_array($options) || empty ($options))return;
        $this->options[] = $options;
    }
    
    private $hasEditPermission = false;
    private function formIntegration($model){
        $this->LoadResource("html", "Html");
        
        $link  = $model. "/edit";
        $custom = "function returnMyLink(cellValue, options, rowdata) {return '';}";
        if(true === $this->LoadModel('usuario/perfil', 'perm')->hasPermission($link)) {
            $custom = $this->editableForm($link);
        }
        $this->grid->setJSCode($custom);
        
    }
    
    private function editableForm($link){
        
        $this->hasEditPermission = true;
        $this->grid->addCol(array(
                "name"=>"Ações","formatter"=> "js:returnMyLink","editable"=> false,
                "sortable"=> false,"resizable"=> true,"fixed"=>true, "width"=> 60
        ), "first");
            
        $edit  = $this->Html->getLink($link)."/";
        if($this->efintegration == ""){
$custom = <<<CUSTOM
        function returnMyLink(cellValue, options, rowdata) {
            var temp = "<a href='$edit" + options.rowId + "' class='ui-icon ui-icon-pencil' target='__blank'></a>";
            return temp;
        }
CUSTOM;
        }else $custom = "function returnMyLink(cellValue, options, rowdata) { $this->efintegration }";
        
        $this->grid->setGridEvent('onSelectRow', 
            "function(rowid, selected){
                if(rowid && rowid !== lastSelection) {
                    $('#$this->list').jqGrid('restoreRow', lastSelection);
                    $('#$this->list').jqGrid('editRow', rowid, true);
                    lastSelection = rowid;
                }
             }");
        return $custom;
    }
    
    public function setEditFormIntegration($funct){
        $this->efintegration = $funct;
    }
    
    private function addOption($key, $optionname, $optionvalue){
        $k = @end(explode(' ',$key));
        $this->arr[$k][$optionname] = $optionvalue;
    }
    
    private function renderOptions(){
        foreach($this->arr as $nm => $arr){
            $this->grid->setColProperty($nm, $arr); 
        }
        $this->arr = array();
    }

}