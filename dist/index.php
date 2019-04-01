<?php
require_once("inc/init.php");
require_once("inc/header.php");

// print_r($_SESSION);

if (isset($_GET['find_vehicle'])) {
  if (
  isset($_GET['id_agency']) && $_GET['id_agency'] != "" &&
  isset($_GET['date_pickup']) && $_GET['date_pickup'] != "" &&
  isset($_GET['date_return']) && $_GET['date_return'] != ""
  ) {
    $id_agency = $_GET['id_agency'];
    $date_pickup = new DateTime($_GET['date_pickup']);
    $date_return = new DateTime($_GET['date_return']);
    $interval = $date_pickup->diff($date_return);
    $nbDays = $interval->format('%a');
    $stmt = $conn->query("SELECT id_vehicle, id_agency, title, description, photo, daily_cost FROM vehicles WHERE id_agency = $id_agency");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // print_r($result);
    if (!empty($result)) {
      $info .= "<div class='col-md-6 mx-auto alert alert-info text-center'>Veuillez choisir un véhicule</div>";
    } else {
      $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Aucun véhicule n'a été trouvé pour ces critères.</div>";
    }
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire.</div>";
  }

  if (!$error) {
    foreach ($result as $key => $value) {
      $content .= "<div class='container'><form action='' method='POST' class='mt-4'><div class='row'>";
      $content .= "<input type='hidden' id='date_pickup' name='date_pickup' value='{$_GET['date_pickup']}'>";
      $content .= "<input type='hidden' id='date_return' name='date_return' value='{$_GET['date_return']}'>";
      
      foreach ($value as $subkey => $subvalue) {
        if ($subkey == 'id_vehicle' || $subkey == 'id_agency') {
          $content .= "<input type='hidden' id='$subkey' name='$subkey' value='$subvalue'>";
        } else if ($subkey == 'photo') {
          $content .= "<div class='col-3'>";
          $content .= "<img src=photos/vehicles/{$subvalue} class='img-fluid'>";
          $content .= "</div>";
        } else if ($subkey == 'daily_cost') {
          $total = $subvalue * $nbDays;
          $content .= "<div class='col-2'>";
          $content .= "<label for='total_cost'>Coût total pour $nbDays jours</label>" ;
          $content .= "<input type='number' id='total_cost' name='total_cost' value=$total readonly class='form-control'>"; 
          $content .= "</div>";      
        } else if ($subkey == 'title') {
          $content .= "<div class='col-2'>";
          $content .= "<h6>$subvalue</h6>";
          $content .= "</div>";
        } else if ($subkey == 'description') {
          $content .= "<div class='col-3'>";
          $content .= "<p>$subvalue</p>";
          $content .= "</div>";
        }
      }
      $content .= "<div class='col-2'><input type='submit' class='btn btn-primary' value='Choisir'></div>";
      $content .= "</div></form></div>";
    }
    
  }
}

if ($_POST & !empty($_POST)) {
  // print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
  }

  
  if (isConnected()) {
    if (
      (isset($_POST['id_agency']) && $_POST['id_agency'] != "") &&
      (isset($_POST['id_vehicle']) && $_POST['id_vehicle'] != "") &&
      (isset($_POST['date_pickup']) && $_POST['date_pickup'] != "") &&
      (isset($_POST['date_return']) && $_POST['date_return'] != "") &&
      (isset($_POST['total_cost']) && $_POST['total_cost'] != "")
    ) {
      $id_user = $_SESSION['user']['id_user'];

      $stmt = $conn->prepare("INSERT INTO orders (id_user, id_vehicle, id_agency, date_pickup, date_return, total_cost) VALUES ($id_user, :id_vehicle, :id_agency, :date_pickup, :date_return, :total_cost)");

      foreach ($_POST as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);   
      }

      $stmt->execute();
      $info .= "<div class='col-md-6 mx-auto alert alert-warning text-center'>Votre commande est bien enregistrée pour le : <strong>" . $_POST['date_pickup'] . '</strong></div>';
      header("Location: pages/orders.php");

    } else {
      $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Une erreur s'est produite, veuillez renouveller la recherche.</div>";
    }
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Veuillez vous identifier pour valider une commande de véhicule.</div>";
  } 
}
?>

<div class="home-hero d-flex flex-column align-items-center">
  <img src="images/hero-image.jpg" alt="hero-image" class="home-hero__image">
  <div class="home-hero__text">
    <h1 class="mt-4 text-center home-hero__title text-white">Louez Véville. Roulez tranquille</h1>
    <h3 class="text-center home-hero__subtitle text-white">Location de véhicule 7j/7, 24h/24</h1>
  </div>
  <form action="" method="GET" class="home-hero__form">
    <div class="row bg-dark pt-2 px-4 rounded-top">
      <div class="form-group px-2">
        <label for="id_agency" class="text-white">Agence de départ</label>
        <select name="id_agency" id="id_agency" class=form-control>
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

      <div class="form-group px-2">
        <label for="date_pickup" class="text-white">Début de location</label>
        <input type="datetime" size="16" class="form-control" name="date_pickup" id="date_pickup" readonly>
        <script type="text/javascript">
        $("#date_pickup").datetimepicker({
          format: 'yyyy-mm-dd hh:ii',
          autoclose: true
        });
        </script>     
      </div>
      
      <div class="form-group px-2">
        <label for="date_return" class="text-white">Fin de location</label>
        <input type="datetime" size="16" class="form-control" name="date_return" id="date_return" readonly>
        <script type="text/javascript">
        $("#date_return").datetimepicker({
          format: 'yyyy-mm-dd hh:ii',
          autoclose: true
        });
        </script>     
      </div>

      <div class="form-group px-2">
        <label for="" class="text-white">Roulez!</label>
        <input type="submit" value="Choisir un véhicule" name="find_vehicle" class="form-control btn btn-info">
      </div>

    </div>  
  </form>
</div>
<?= $info ?>
<?= $content ?>
<img src="images/bg-image.jpg" alt="background-image" class="img-fluid">


<?php
require_once("inc/footer.php");
?>