<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");


if (!isConnectedAsAdmin()) {
  header("Location: pages/connexion.php");
}

// to remove agencies
if (isset($_GET['action']) && $_GET['action'] == 'remove'){
  // requete de suppression
  $result = $conn->prepare("DELETE FROM agencies WHERE id_agency = :id_agency");
  $result->bindValue(':id_agency', $_GET['id_agency'], PDO::PARAM_INT);
  $result->execute();

  $info .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>L'agence n° <strong>$_GET[id_agency]</strong> a bien été supprimée.</div>";
}

// to modify agencies
if (isset($_GET['action']) && $_GET['action'] == 'modify') {
  // verif sécu
  if (isset($_GET['id_agency'])) {
    $result = $conn->prepare("SELECT * FROM agencies WHERE id_agency = :id_agency");
    $result->bindValue(':id_agency', $_GET['id_agency']);
    $result->execute();

    $this_agency = $result->fetch(PDO::FETCH_ASSOC);
    // print_r($this_agency);
  }

  $agency_id = (isset($this_agency['id_agency'])) ? $this_agency['id_agency'] : '';
} 

if ($_POST){
  // print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (
    (isset($_POST['title']) && $_POST['title'] != "") &&
    (isset($_POST['address']) && $_POST['address'] != "") &&
    (isset($_POST['city']) && $_POST['city'] != "") &&
    (isset($_POST['zip_code']) && $_POST['zip_code'] != "") &&
    (isset($_POST['description']) && $_POST['description'] != "") &&
    (isset($_POST['photo']) && $_POST['photo'] != "")
    ) { 
    if (isset($_POST['modify'])) { 
      $info .= "<div class='col-md-6 mx-auto alert alert-warning text-center'>L'agence : <strong>" . $_POST['title'] . '</strong> a bien été modifiée !!</div>';
    } else {
      $info .= "<div class='col-md-6 mx-auto alert alert-success text-center'>L'agence : <strong>" . $_POST['title'] . '</strong> a bien été ajoutée !!</div>';
    }
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire</div>";
  }

  if (!$error) {
    // modification statement
    if (isset($_POST['modify'])) {
      $result = $conn->prepare("UPDATE agencies SET title = :title, address = :address, city = :city, zip_code = :zip_code, description = :description, photo = :photo WHERE id_agency = :id_agency");
    } else {
      $result = $conn->prepare("INSERT INTO agencies (title, address, city, zip_code, description, photo) VALUES (:title, :address, :city, :zip_code, :description, :photo)");
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

<h1 class="m-4 text-center">Ajout/modification d'agences</h1>

<?php echo $info ?>
<?php echo $error ?>
  
<!-- vehicles table content into HTML table -->
<div id="table-container" style="width: 1140px; height: 420px" class="mb-4 overflow-auto table-fix-head">
<?php 
$content = "";

$stmt = $conn->query("SELECT * FROM agencies");
$result =  $stmt->fetchAll(PDO::FETCH_ASSOC);

$content .= '<table class="table"><tr>';
$content .= "<th>Agence</th><th>Titre</th><th>Adresse</th><th>Ville</th><th>CP</th><th>Description</th><th>Photo</th>";

$content .= '<th>Modifier</th>';
$content .= '<th>Supprimer</th>';
$content .= '</tr>';
foreach ($result as $key => $value) {
  $content .= '<tr>';
  foreach ($value as $subkey => $subvalue) {
    // $content .= '<pre>'; print_r($value); $content .= '</pre>';
    if ($subkey == 'photo')
      $content .= "<td><img src='../../photos/agencies/$subvalue' width='150'></td>";
  
    else
    $content .= "<td>$subvalue</td>";
  }
  $content .= '<td><a href="?action=modify&id_agency=' . $value['id_agency'] . '">X</a></td>';
  $content .= '<td><a href="?action=remove&id_agency=' . $value['id_agency'] . '">X</a></td>';
  $content .= '</tr>';
}
$content .= '</table>';

echo $content;
?>
</div>
  
<div class="container">
<!-- Vehicle insert form -->
  <form action="" method="POST" class="mt-4">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="title">Titre de l'agence</label>
          <input type="text" id="title" name="title" <?= 'value="', (isset($this_agency)) ? $this_agency['title'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="description">Description de l'agence</label>
          <textarea id="description" name="description" class="form-control" cols="30" rows="6"><?= (isset($this_agency)) ? $this_agency['description'] : '' ?></textarea>
        </div>
        <div class="form-group">
          <label for="photo">Photo de l'agence</label>
          <input type="file" name="photo" id="photo" <?= 'value="', (isset($this_agency)) ? $this_agency['photo'] : '', '"' ?> class="form-control">
        </div>
        
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="address">Adresse</label>
          <input type="text" id="address" name="address" <?= 'value="', (isset($this_agency)) ? $this_agency['address'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="city">Ville</label>
          <input type="text" id="city" name="city" <?= 'value="', (isset($this_agency)) ? $this_agency['city'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="zip_code">Code Postal</label>
          <input type="number"  id="zip_code" name="zip_code" <?= 'value="', (isset($this_agency)) ? $this_agency['zip_code'] : '', '"' ?> class="form-control">
        </div>
        
          
        <!-- submits inputs -->
        <?php 
        $content = "";
        if (isset($_GET['action']) && $_GET['action'] == 'modify') {
          // agency modification inputs
          $content .= '<div class="form-group row">';
          $content .= '<label for="id_agency" class="col-sm-3 col-form-label">Agence n°</label>';
          $content .= '<input id="id_agency" name="id_agency" type="text" readonly class="form-control col-2" value="' . $_GET['id_agency'] . '">';
          $content .= '<input id="modify-btn" class="btn btn-warning ml-2" type="submit" value="Modifier" name="modify">';
          $content .= '<a  class="btn btn-info ml-2" href="javascript:window.location = window.location.href.split(' . "'?'" . ')[0]">Annuler</a></div>';

          echo $content;
        } else {
          // submit agency input
          echo '<input id="submit-btn" class="btn btn-primary" type="submit" value="Enregistrer">';
        }
        ?>
        
      </div>
    </div>
  
  
  </form>
</div>
</section>
<script src="<?=SCRIPTS?>jquery-3.3.1.min.js"></script>


<?php

require_once("../../inc/footer.php");
?>