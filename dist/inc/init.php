<?php
// $conn = new PDO('mysql:host=localhost;dbname=veville', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
$conn = new PDO('mysql:host=sql24;dbname=rne28651', 'rne28651', '8qjMfdzuv6m6', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// ----- SESSION
session_start();

// ----- PATH
define("ROOT", $_SERVER['DOCUMENT_ROOT']. "/Veville/dist/");
// echo ROOT;

// cette constante retourne le chemin physique du dossier véville sur le serveur lors le l'enregistrement d'images/photos, nous aurons besoin du chemin complet du dossier photo pour enregistrer la photo.


define("INC", "inc/");

define("PAGES", "pages/");
// cette constante servira à enregistrer l'URL d'une photo/image dans la BDD, on ne conserve jamais la photo elle même, ce serait trop lourd pour le serveur.

define("ADMIN", "pages/admin/");

define("IMAGES", "images/");

// ------- VARIABLES
$content = '';
$error = '';
$profile = Array();

// ------- INCLUSIONS 
require_once("functions.php");
?>