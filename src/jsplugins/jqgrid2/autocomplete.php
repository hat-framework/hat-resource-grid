<?php

require_once '../../../../init.php';
require_once "jqgridGrid.php";
$model = filter_input(INPUT_GET, 'model');
$limit = filter_input(INPUT_GET, 'limit');
$q     = filter_input(INPUT_GET, 'q');
if($model == ""){die(json_encode(array('status'=>'0','statusText'=>'Modelo não informado!')));}
if($q     == ""){die(json_encode(array('status'=>'0','statusText'=>'String de busca não informada!')));}
if($limit == ""){$limit = '10';}

try{
    $obj = new classes\Classes\Object();
    $md         = $obj->LoadModel($model, 'md');
    $pkey       = $md->getPkey();
    $title      = $md->getModelTitle();
    $searchable = $md->getModelSearchableKeys();
    if(empty($searchable)){$searchable = array($title);}
    if($title === "" || $pkey === ""){die(json_encode(array()));}
    
    $query = array();
    foreach($searchable as $s){
        $query[] = "$s LIKE '$q%'";
    }
    $qr = implode(" OR ", $query);
    die(json_encode($md->selecionar(array($pkey, $title), $qr, $limit)));
}catch(Exception $e){die(json_encode(array('status'=>'0','statusText'=>$e->getMessage())));}
