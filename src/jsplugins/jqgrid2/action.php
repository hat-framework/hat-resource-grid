<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once "./jqgridGrid.php";
$grid = new jqgridGrid("");
$grid->execute("", $_GET['model']);
