<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");
?>

<?php echo $content ?>
<div class="container">
  <h1 class="mt-4 text-center">Ajout de véhicules</h1>
  <?php echo $error ?>
  <form action="" method="POST" class="mt-4 col-md-4 offset-4">
    <div class="form-group">
      <label for="title">Titre du véhicule</label>
      <input type="text" id="title" name="title" class="form-control">
    </div>
    <div class="form-group">
      <label for="brand">Marque</label>
      <input type="text" id="brand" name="brand" class="form-control">
    </div>
    <div class="form-group">
      <label for="model">Modèle</label>
      <input type="text" id="model" name="model" class="form-control">
    </div>
    <div class="form-group">
      <label for="description">Description du véhicule</label>
      <textarea id="description" name="description" class="form-control"cols="30" rows="10"></textarea>
    </div>
    <div class="form-group">
      <label for="photo">Photo du véhicule</label>
      <input type="file" name="photo" id="photo" class="form-control">
    </div>
    <div class="form-group">
      <label for="daily_cost">Coût journalier</label>
      <input type="number" id="daily_cost" name="daily_cost" class="form-control">
    </div>
    <input type="submit" value="valider">
  </form>
</div>
<?php
require_once("../../inc/footer.php");
?>