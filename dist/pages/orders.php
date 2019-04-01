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
  </table>
</div>
<img src="../images/bg-image.jpg" alt="" class="img-fluid">
<?php
require_once("../inc/footer.php");
?>