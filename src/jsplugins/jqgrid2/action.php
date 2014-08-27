<?php

require_once '../../../../../../init.php';
require_once "./jqgridGrid.php";
$grid = new jqgridGrid("");
$grid->execute("", $_GET['model']);
