<?php
require_once("../inc/init.php");
require_once("../inc/header.php");


if (!isConnected()) {
  header("Location: pages/connexion.php");
}
?>

<div class="container">
  <h1></h1>
</div>

<?php
require_once("../inc/footer.php");
?>