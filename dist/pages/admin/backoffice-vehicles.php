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

  $content .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>Le véhicule n° <strong>$_GET[id_vehicle]</strong> a bien été supprimé.</div>";
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
} else

if ($_POST){

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (count($_POST) === 7){
    $content .= "<div class='col-md-6 mx-auto alert alert-success text-center'>Le véhicule : <strong>" . $_POST['title'] . '</strong> a bien été ajouté !!</div>';;
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de remplir tous les champs du formulaire</div>";
  }

  if (!$error) {

    // form insert query
    $result = $conn->prepare("INSERT INTO vehicles (id_agency, title, brand, model, description, photo, daily_cost) VALUES (:id_agency, :title, :brand, :model, :description, :photo, :daily_cost)");

    foreach ($_POST as $key => $value) {    
      $result->bindValue(":$key", $value, PDO::PARAM_STR);   
    }

    $result->execute();

  }
}
?>

<!-- Vehicle insert form -->


<h1 class="m-4 text-center">Ajout de véhicules</h1>

<?php echo $content ?>
<?php echo $error ?>
<form action="" method="POST" class="mt-4">
  <div class="form-group col-md-4">
    <label for="id_agency">Nom de l'agence</label>
    <select name="id_agency" id="id_agency" <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['id_agency'] : '', '"' ?>>
    <?php
      
      $stmt = $conn->query("SELECT * FROM agencies");
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $option = '';
      foreach ($result as $key => $value) {
        $optionStatus = (isset($this_vehicle) && ($this_vehicle['id_agency'] == 
        $value['id_agency'])) ? "selected" : "";
        // echo $optionStatus;
        $option = "<option value=" . $value['id_agency'] . ' ' . $optionStatus . ">" . $value['title'] . "</option>";
        echo $option;
      }
      ?>
    </select>
  </div>
  <!-- vehicles table content into HTML table -->
  <div id="table-container" style="width: 1140px; height: 420px" class="mb-4 overflow-auto">></div>
    
  </table>

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
        
        <input id="submit-btn" class="btn btn-primary" type="submit" value="Enregistrer"
        <?= 'style="display: ', (isset($_GET['action']) && $_GET['action'] == 'modify') ? 'none"' : 'inline"' ?>
        >  

        <input id="modify-btn" class="btn btn-warning"type="submit" value="Modifier" 
        <?= 'style="display: ', (isset($_GET['action']) && $_GET['action'] == 'modify') ? 'inline"' : 'none"' ?>
        >    
      </div>
    </div>
  </div>
  
  
</form>

<script src="<?=SCRIPTS?>jquery-3.3.1.min.js"></script>

<script>
// car brands from json file
let dropdown = $('#brand-names');

dropdown.empty();

dropdown.append('<option selected="true" disabled>marque du véhicule</option>');
dropdown.prop('selectedIndex', 0);

const url = '../../data/brands.json';

// Populate dropdown with list of car brands
$.getJSON(url, function (data) {
  $.each(data, function (key, entry) {
    dropdown.append($('<option></option>').attr('value', key).text(key));
  })
});
</script>

<script src="<?=SCRIPTS?>ajax-vehicles.js"></script>
<?php
// require_once("./toAjax-vehicles.php");
require_once("../../inc/footer.php");
?>