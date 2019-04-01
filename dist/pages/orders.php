<?php
require_once("../inc/init.php");
require_once("../inc/header.php");


if (!isConnected()) {
  header("Location: pages/connexion.php");
}
?>

<div class="container">
  <h2 class="m-4 text-center">Mes commandes</h2>
  <table class="table">
    <tr>
      <th>Numéro de commande</th>
      <th>Agence de départ</th>
      <th>Date et heure de départ</th>
      <th>Date et heure de retour</th>
      <th>Coût total</th>
      <th>Date de la commande</th>
    </tr>
    <?php
    $id_user = $_SESSION['user']['id_user'];
    $stmt = $conn->query("SELECT id_order, agencies.title, date_pickup, date_return, total_cost, date_order FROM orders LEFT JOIN agencies ON orders.id_agency = agencies.id_agency WHERE orders.id_user = $id_user");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $key => $value) {
      echo "<tr>";
      foreach ($value as $subkey => $subvalue) {
        if ($subkey == 'total_cost') echo "<td>$subvalue €</td>";
        else echo "<td>$subvalue</td>";
      }
      echo "</tr>";
    }
    ?>
  </table>
</div>
<img src="../images/bg-image.jpg" alt="" class="img-fluid">
<?php
require_once("../inc/footer.php");
?>