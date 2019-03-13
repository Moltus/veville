<?php

// fonction pour voir si l'internaute est inscrit / connecté, c'est à dire qu'il existe un indice "membre" dans son fichier session
function isConnected(){
  if(!isset($_SESSION['user']))
    return false;
  else
    return true;
}

// fonction pour vérifier s'il est connecté en tant qu'admin avec le statut 1 : admin
function isConnectedAsAdmin(){
  if (isConnected() && $_SESSION['user']['status'] == 1)
    return true;
  else
    return false;
}
?>