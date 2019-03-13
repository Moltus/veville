<?php
$conn = new PDO('mysql:host=localhost;dbname=veville', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// ----- SESSION
session_start();

// ----- PATH
define("ROOT", $_SERVER['DOCUMENT_ROOT']. "/Veville/dist/");
// echo ROOT;

// cette constante retourne le chemin physique du dossier véville sur le serveur lors le l'enregistrement d'images/photos, nous aurons besoin du chemin complet du dossier photo pour enregistrer la photo.


define("INC", "http://localhost/Veville/dist/inc/");

define("PAGES", "http://localhost/Veville/dist/pages/");
// cette constante servira à enregistrer l'URL d'une photo/image dans la BDD, on ne conserve jamais la photo elle même, ce serait trop lourd pour le serveur.

define("ADMIN", "http://localhost/Veville/dist/pages/admin/");

define("IMAGES", "http://localhost/Veville/dist/images/");

// ------- VARIABLES
$content = '';
$error = '';
$profile = Array();

// ------- INCLUSIONS 
require_once("functions.php");
?>