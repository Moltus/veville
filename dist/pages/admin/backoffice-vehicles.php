<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");


if (!isConnectedAsAdmin()) {
  header("Location: pages/connexion.php");
}

// to remove vehicles
if (isset($_GET['action']) && $_GET['action'] == 'remove'){
  // requete de suppression
  $result = $conn->prepare("DELETE FROM vehicles WHERE id_vehicle = :id_vehicle");
  $result->bindValue(':id_vehicle', $_GET['id_vehicle'], PDO::PARAM_INT);
  $result->execute();

  $info .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>Le véhicule n° <strong>$_GET[id_vehicle]</strong> a bien été supprimé.</div>";
}

// to modify vehicles
if (isset($_GET['action']) && $_GET['action'] == 'modify') {
  // verif sécu
  if (isset($_GET['id_vehicle'])) {
    $result = $conn->prepare("SELECT * FROM vehicles WHERE id_vehicle = :id_vehicle");
    $result->bindValue(':id_vehicle', $_GET['id_vehicle']);
    $result->execute();

    $this_vehicle = $result->fetch(PDO::FETCH_ASSOC);
    // print_r($this_vehicle);
  }

  $vehicle_id = (isset($this_vehicle['id_vehicle'])) ? $this_vehicle['id_vehicle'] : '';
} 

if ($_POST & !empty($_POST)){
  // print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (
    (isset($_POST['id_agency']) && $_POST['id_agency'] != "") &&
    (isset($_POST['title']) && $_POST['title'] != "") &&
    (isset($_POST['brand']) && $_POST['brand'] != "") &&
    (isset($_POST['model']) && $_POST['model'] != "") &&
    (isset($_POST['daily_cost']) && $_POST['daily_cost'] != "") &&
    (isset($_POST['photo']) && $_POST['photo'] != "") &&
    (isset($_POST['description']) && $_POST['description'] != "")
    ) { 
    if (isset($_POST['modify'])) { 
      $info .= "<div class='col-md-6 mx-auto alert alert-warning text-center'>Le véhicule : <strong>" . $_POST['title'] . '</strong> a bien été modifié !!</div>';
    } else {
      $info .= "<div class='col-md-6 mx-auto alert alert-success text-center'>Le véhicule : <strong>" . $_POST['title'] . '</strong> a bien été ajouté !!</div>';
    }
  } else {
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire</div>";
  }

  if (!$error) {
    // modification statement
    if (isset($_POST['modify'])) {
      $result = $conn->prepare("UPDATE vehicles SET id_agency = :id_agency, title = :title, brand = :brand, model= :model, description = :description, photo = :photo, daily_cost = :daily_cost WHERE id_vehicle = :id_vehicle");
    } else {
      $result = $conn->prepare("INSERT INTO vehicles (id_agency, title, brand, model, description, photo, daily_cost) VALUES (:id_agency, :title, :brand, :model, :description, :photo, :daily_cost)");
    }

    foreach ($_POST as $key => $value) {
      if ($key != 'modify') {    
        $result->bindValue(":$key", $value, PDO::PARAM_STR);   
      }
    }

    $result->execute();

  }
}
?>

<!-- HTML -->
<section class="container pb-4">
<h2 class="m-4 text-center">Ajout/modification de véhicules</h2>

<?php echo $info ?>
<?php echo $error ?>

<!-- Vehicle insert/modify form -->
<form action="" method="POST" class="mt-4">
  <div class="form-group col-md-4">
    <label for="id_agency">Nom de l'agence</label>
    <select name="id_agency" id="id_agency">
    <?php
      
      $stmt = $conn->query("SELECT * FROM agencies");
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $option = '';
      foreach ($result as $key => $value) {
        $optionStatus = (isset($this_vehicle) && ($this_vehicle['id_agency'] == 
        $value['id_agency'])) ? "selected" : "";
        $option = "<option value=" . $value['id_agency'] . ' ' . $optionStatus . ">" . $value['title'] . "</option>";
        echo $option;
      }
      ?>
    </select>
  </div>
  <!-- vehicles table content into HTML table -->
  <div id="table-container" style="width: 1140px; height: 420px" class="mb-4 overflow-auto table-fix-head"></div>

  <!-- rest of the form -->
  <div class="container">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="title">Titre du véhicule</label>
          <input type="text" id="title" name="title" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['title'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="brand">Marque</label>
          <input type="text" id="brand" list="brand-names" name="brand" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['brand'] : '', '"' ?> class="form-control">
          <datalist id="brand-names">
          <!-- insert with jquery script -->
          </datalist>
        </div>
        <div class="form-group">
          <label for="model">Modèle</label>
          <input type="text" id="model" name="model" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['model'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="daily_cost">Coût journalier</label>
          <input type="number" step="0.01" id="daily_cost" name="daily_cost" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['daily_cost'] : '', '"' ?> class="form-control">
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="photo">Photo du véhicule</label>
          <input type="file" name="photo" id="photo" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['photo'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="description">Description du véhicule</label>
          <textarea id="description" name="description" class="form-control" cols="30" rows="6"><?= (isset($this_vehicle)) ? $this_vehicle['description'] : '' ?></textarea>
        </div>

        <!-- submits inputs -->
        <?php 
        $content = "";
        if (isset($_GET['action']) && $_GET['action'] == 'modify') {
          // vehicle modification inputs
          $content .= '<div class="form-group row">';
          $content .= '<label for="id_vehicle" class="col-sm-3 col-form-label">Véhicule n°</label>';
          $content .= '<input id="id_vehicle" name="id_vehicle" type="text" readonly class="form-control col-2" value="' . $_GET['id_vehicle'] . '">';
          $content .= '<input id="modify-btn" class="btn btn-warning ml-2" type="submit" value="Modifier" name="modify">';
          $content .= '<a  class="btn btn-info ml-2" href="javascript:window.location = window.location.href.split(' . "'?'" . ')[0]">Annuler</a></div>';

          echo $content;
        } else {
          // submit vehicle input
          echo '<input id="submit-btn" class="btn btn-primary" type="submit" value="Enregistrer">';
        }
        ?>
        
      </div>
    </div>
  </div>
  
  
</form>
</section>
<script src="<?=SCRIPTS?>jquery-3.3.1.min.js"></script>

<script src="<?=SCRIPTS?>ajax-vehicles.js"></script>
<?php
// require_once("./toAjax-vehicles.php");
require_once("../../inc/footer.php");
?>