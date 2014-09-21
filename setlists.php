<?php
require_once("resources/core.php");
$activeMenuItem = "Set Lists";

$songID = "";
if(isset($_GET['songID']) && $_GET['songID'])
    $songID=$_GET['songID'];
$query = "SELECT * FROM sets ORDER BY upper(name) ASC";
$results = $db->get_results($query);
include('resources/templates/head.php');
include('resources/templates/setlists.php');
include('resources/templates/foot.php');
?>