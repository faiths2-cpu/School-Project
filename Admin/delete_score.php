<?php
session_start();
include "admin_PDO.php";

if(!isset($_SESSION['USERNAME'])){
    header("location:../index.php");
}

if(isset($_GET['score_id'])) {
    $hybrid = new admin_PDO();
    $hybrid->deleteScore($_GET['score_id']);
    header("location:index.php"); } ?>