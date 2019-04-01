<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");


if (!isConnectedAsAdmin()) {
  header("Location: pages/connexion.php");
}

// to remove orders
if (isset($_GET['action']) && $_GET['action'] == 'remove'){
  // requete de suppression
  $result = $conn->prepare("DELETE FROM orders WHERE id_order = :id_order");
  $result->bindValue(':id_order', $_GET['id_order'], PDO::PARAM_INT);
  $result->execute();

  $info .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>La commande n° <strong>$_GET[id_order]</strong> a bien été supprimée.</div>";
}

// to modify orders
if (isset($_GET['action']) && $_GET['action'] == 'modify') {
  // verif sécu
  if (isset($_GET['id_order'])) {
    $result = $conn->prepare("SELECT * FROM orders WHERE id_order = :id_order");
    $result->bindValue(':id_order', $_GET['id_order']);
    $result->execute();

    $this_order = $result->fetch(PDO::FETCH_ASSOC);
    // print_r($this_order);
  }

  $order_id = (isset($this_order['id_order'])) ? $this_order['id_order'] : '';
} 

if ($_POST & !empty($_POST)){
  // print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (
    (isset($_POST['modify'])) &&
    (isset($_POST['id_order']) && $_POST['id_order'] != "") &&
    (isset($_POST['id_user']) && $_POST['id_user'] != "") &&
    (isset($_POST['id_vehicle']) && $_POST['id_vehicle'] != "") &&
    (isset($_POST['date_pickup']) && $_POST['date_pickup'] != "") &&
    (isset($_POST['date_return']) && $_POST['date_return'] != "") &&
    (isset($_POST['total_cost']) && $_POST['total_cost'] != "") &&
    (isset($_POST['date_order']) && $_POST['date_order'] != "")
  ) { 
    $info .= "<div class='col-md-6 mx-auto alert alert-warning text-center'>La commande : <strong>" . $_POST['id_order'] . '</strong> a bien été modifiée !!</div>';
    
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire</div>";
  }

  if (!$error) {
    // modification statement
    $result = $conn->prepare("UPDATE orders SET id_user = :id_user, id_vehicle = :id_vehicle, id_agency = :id_agency, date_pickup = :date_pickup, date_return = :date_return, total_cost = :total_cost, date_order = :date_order WHERE id_order = :id_order");

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
<h2 class="m-4 text-center">Gestion des commandes</h2>

<?php echo $info ?>
<?php echo $error ?>

<!-- order insert/modify form -->
<form action="" method="POST" class="mt-4">
  <div class="form-group col-md-4">
    <label for="id_agency">Nom de l'agence</label>
    <select name="id_agency" id="id_agency">
    <?php
      
      $stmt = $conn->query("SELECT * FROM agencies");
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $option = '';
      foreach ($result as $key => $value) {
        $optionStatus = (isset($this_order) && ($this_order['id_agency'] == 
        $value['id_agency'])) ? "selected" : "";
        $option = "<option value=" . $value['id_agency'] . ' ' . $optionStatus . ">" . $value['title'] . "</option>";
        echo $option;
      }
      ?>
    </select>
  </div>
  <!-- orders table content into HTML table -->
  <div id="table-container" style="width: 1140px; height: 420px" class="mb-4 overflow-auto table-fix-head"></div>

  <!-- rest of the form -->
  <div class="container">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="id_user">Id utilisateur</label>
          <input type="number" id="id_user" name="id_user" <?= 'value="', (isset($this_order)) ? $this_order['id_user'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
        <label for="id_vehicle">Id véhicule</label>
          <input type="number" id="id_vehicle" name="id_vehicle" <?= 'value="', (isset($this_order)) ? $this_order['id_vehicle'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="date_pickup">Date et heure de départ</label>
          <input type="text" id="date_pickup" name="date_pickup" <?= 'value="', (isset($this_order)) ? $this_order['date_pickup'] : '', '"' ?> class="form-control">
        </div>
        
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="date_return">Date et heure de retour</label>
          <input type="text" id="date_return" name="date_return" <?= 'value="', (isset($this_order)) ? $this_order['date_return'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="total_cost">Coût total</label>
          <input type="number" name="total_cost" id="total_cost" <?= 'value="', (isset($this_order)) ? $this_order['total_cost'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="date_order">Date et heure de commande</label>
          <input type="text" id="date_order" name="date_order" <?= 'value="', (isset($this_order)) ? $this_order['date_order'] : '', '"' ?> class="form-control">
        </div>

        <!-- submits inputs -->
        <?php 
        $content = "";
        if (isset($_GET['action']) && $_GET['action'] == 'modify') {
          // order modification inputs
          $content .= '<div class="form-group row">';
          $content .= '<label for="id_order" class="col-sm-3 col-form-label">Commande n°</label>';
          $content .= '<input id="id_order" name="id_order" type="number" readonly class="form-control col-2" value="' . $_GET['id_order'] . '">';
          $content .= '<input id="modify-btn" class="btn btn-warning ml-2" type="submit" value="Modifier" name="modify">';
          $content .= '<a  class="btn btn-info ml-2" href="javascript:window.location = window.location.href.split(' . "'?'" . ')[0]">Annuler</a></div>';

          echo $content;
        } else {
          // submit order input
          echo '<input id="submit-btn" class="btn btn-primary" type="submit" value="Enregistrer">';
        }
        ?>
        
      </div>
    </div>
  </div>
  
  
</form>
</section>
<script src="<?=SCRIPTS?>jquery-3.3.1.min.js"></script>

<script src="<?=SCRIPTS?>ajax-orders.js"></script>
<?php
// require_once("./toAjax-orders.php");
require_once("../../inc/footer.php");
?>