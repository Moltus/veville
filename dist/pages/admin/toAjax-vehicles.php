<?php

require_once("../../inc/init.php");
// extract($_POST);
// $id_agency = 3;
$id_agency = $_GET['id_agency'];
// echo $id_agency;

$content = '';
// echo $id_agency;
$stmt = $conn->query("SELECT id_vehicle, agencies.title AS title1, vehicles.title AS title2, brand, model, vehicles.description, vehicles.photo, daily_cost FROM vehicles LEFT JOIN agencies ON vehicles.id_agency = agencies.id_agency WHERE vehicles.id_agency = $id_agency");
$result =  $stmt->fetchAll(PDO::FETCH_ASSOC);

// print_r($result);
$content .= '<table class="table"><tr>';
$content .= "<th>Véhicule</th><th>Agence</th><th>Titre</th><th>Marque</th><th>Modèle</th><th>Description</th><th>Photo</th><th>Coût/jour</th>";

$content .= '<th>Modifier</th>';
$content .= '<th>Supprimer</th>';
$content .= '</tr>';
foreach ($result as $key => $value) {
  $content .= '<tr>';
  foreach ($value as $subkey => $subvalue) {
    // $content .= '<pre>'; print_r($value); $content .= '</pre>';
    if ($subkey == 'photo')
      $content .= "<td><img src='../../photos/vehicles/$subvalue' width='150'></td>";
    else if ($subkey == 'daily_cost')
      $content .= "<td>$subvalue €</td>";
    else
     $content .= "<td>$subvalue</td>";
  }
  $content .= '<td><a href="?action=modify&id_vehicle=' . $value['id_vehicle'] . '">X</a></td>';
  $content .= '<td><a href="?action=remove&id_vehicle=' . $value['id_vehicle'] . '">X</a></td>';
  $content .= '</tr>';
}
$content .= '</table>';

echo ($content);
// json_encode($content)
// echo $content;
?>