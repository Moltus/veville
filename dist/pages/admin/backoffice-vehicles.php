<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");

$stmt = $conn->query("SELECT title FROM agencies");
$agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_POST){
  print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (count($_POST) === 7){
    echo '<strong>Formulaire correctement rempli</strong>';
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de remplir tous les champs du formulaire</div>";
  }

  if (!$error) {
    $agencyName = $_POST['agency'];
    // echo $agencyName;
    $stmt = $conn->query("SELECT id_agency FROM agencies WHERE title='$agencyName'");
    $getAgencyId = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $AgencyId = $getAgencyId[0]['id_agency'];
    print_r($getAgencyId);
    // Réaliser la requête d'insertion à la validation du formulaire
    $result = $conn->prepare("INSERT INTO vehicles (id_agency, title, brand, model, description, photo, daily_cost) VALUES ($AgencyId, :title, :brand, :model, :description, :photo, :daily_cost)");

    // // $result->bindValue(':id_produit', $_GET['id_produit']);
    $result->bindValue(':title', $_POST['title']);
    $result->bindValue(':brand', $_POST['brand']);
    $result->bindValue(':model', $_POST['model']);
    $result->bindValue(':description', $_POST['description']);
    $result->bindValue(':photo', $_POST['photo']);
    $result->bindValue(':daily_cost', $_POST['daily_cost']);

    // foreach ($_POST as $key => $value) {    
    //   $result->bindValue(":$key", $value, PDO::PARAM_STR);   
    // }

    $result->execute();

  }
}
?>

<?php echo $content ?>
<div class="container">
  <h1 class="mt-4 text-center">Ajout de véhicules</h1>
  <?php echo $error ?>
  <form action="" method="POST" class="mt-4 col-md-4 offset-4">
    <div class="form-group">
      <label for="agency">Nom de l'agence</label>
      <!-- <input type="text" id="agency" name="agency" class="form-control"> -->
      <select name="agency" id="agency">
      <?php foreach ($agencies as $key => $value) {
        foreach ($value as $key2 => $agency) {
          echo "<option value='$agency'>$agency</option>";
        }
      }
      ?>
      </select>
    </div>
    <div class="form-group">
      <label for="title">Titre du véhicule</label>
      <input type="text" id="title" name="title" class="form-control">
    </div>
    <div class="form-group">
      <label for="brand">Marque</label>
      <input type="text" id="brand" list="brand-names" name="brand" class="form-control">
      <datalist id="brand-names">
      <!-- insert with jquery script -->
      </datalist>
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
      <input type="number" step="0.01" id="daily_cost" name="daily_cost" class="form-control">
    </div>
    <input type="submit" value="valider">
  </form>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script>
  let dropdown = $('#brand-names');

  dropdown.empty();

  dropdown.append('<option selected="true" disabled>marque du véhicule</option>');
  dropdown.prop('selectedIndex', 0);

  const url = '../../data/brands.json';

  // Populate dropdown with list of car brands
  $.getJSON(url, function(data) {
    $.each(data, function(key, entry) {
      dropdown.append($('<option></option>').attr('value', key).text(key));
    })
  });
</script>
<?php
require_once("../../inc/footer.php");
?>