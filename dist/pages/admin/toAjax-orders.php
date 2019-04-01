<?php

require_once("../../inc/init.php");

$id_agency = $_GET['id_agency'];

$content = '';
// echo $id_agency;
$stmt = $conn->query("SELECT orders.id_order, users.id_user, users.family_name, users.first_name, users.email, orders.id_vehicle, vehicles.title, orders.date_pickup, orders.date_return, orders.total_cost, orders.date_order FROM orders LEFT JOIN users ON orders.id_user = users.id_user LEFT JOIN vehicles ON orders.id_vehicle = vehicles.id_vehicle WHERE orders.id_agency = $id_agency");
$result =  $stmt->fetchAll(PDO::FETCH_ASSOC);

// print_r($result);
$content .= '<table class="table"><tr>';
$content .= "<th>Commande</th><th>Utilisateur</th><th>Véhicule</th><th>Date et heure de départ</th><th>Date et heure de retour</th><th>Coût total</th><th>Date et heure de commande</th>";

$content .= '<th>Modifier</th>';
$content .= '<th>Supprimer</th>';
$content .= '</tr>';
foreach ($result as $key => $value) {

  $content .= '<tr>';
  $content .= "<td>{$value['id_order']}</td>";
  $content .= "<td>{$value['id_user']} - {$value['first_name']} {$value['family_name']} - {$value['email']}</td>";
  $content .= "<td>{$value['id_vehicle']} - {$value['title']}</td>";
  $content .= "<td>{$value['date_pickup']}</td>";
  $content .= "<td>{$value['date_return']}</td>";
  $content .= "<td>{$value['total_cost']}</td>";
  $content .= "<td>{$value['date_order']}</td>";

  $content .= '<td><a href="?action=modify&id_order=' . $value['id_order'] . '">X</a></td>';
  $content .= '<td><a href="?action=remove&id_order=' . $value['id_order'] . '">X</a></td>';
  $content .= '</tr>';
}
$content .= '</table>';

echo ($content);
// json_encode($content)
// echo $content;
?>