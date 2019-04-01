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
    // echo ($date_pickup < $date_return) ? "date retour bien supérieur" : "date retour inférieure ! danger";
    if ($date_pickup > $date_return) {
      $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Attention de bien préciser une date de retour ultérieure à la date de départ.</div>";
    }
    $interval = $date_pickup->diff($date_return);
    $nbDays = $interval->format('%a');
    if ($nbDays < 1) {
      $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>La location de véhicules est prévue pour une journée au minimum.</div>";
    }

    if (isset($_GET['order']) && $_GET['order'] == 'ascending') {
      $stmt = $conn->query("SELECT id_vehicle, id_agency, title, description, photo, daily_cost FROM vehicles WHERE id_agency = $id_agency ORDER BY daily_cost ASC");
    } else if (isset($_GET['order']) && $_GET['order'] == 'descending') {
      $stmt = $conn->query("SELECT id_vehicle, id_agency, title, description, photo, daily_cost FROM vehicles WHERE id_agency = $id_agency ORDER BY daily_cost DESC");
    } else {
      $stmt = $conn->query("SELECT id_vehicle, id_agency, title, description, photo, daily_cost FROM vehicles WHERE id_agency = $id_agency");
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // print_r($result);
    if (!empty($result)) {
      $info .= "<div class='col-md-6 mx-auto alert alert-info text-center'>Veuillez choisir un véhicule</div>";

      // echo "before filter : ";
      // print_r($result);
      // echo "<br>";

      foreach ($result as $key => $value) {
        $id_vehicle = $value['id_vehicle'];
        // echo "id : ", $id_vehicle, " - ";
        $stmt = $conn->query("SELECT date_pickup, date_return FROM vehicles LEFT JOIN orders ON vehicles.id_vehicle = orders.id_vehicle WHERE vehicles.id_vehicle = $id_vehicle");
        $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // print_r($result2);
        // echo $result2[0]['date_pickup'];
        // echo $result2[0]['date_return'];
        if (
          ($_GET['date_return'] >= $result2[0]['date_pickup'] && 
          $_GET['date_return'] <= $result2[0]['date_return'])
          || 
          ($_GET['date_pickup'] <= $result2[0]['date_return'] && 
          $_GET['date_pickup'] >= $result2[0]['date_pickup'])
          ) {
          // echo "key : $key, id_vehicule : $id_vehicle";
          unset($result[$key]);
        }
      }

      // echo "<br>after filter : ";
      // print_r($result);

    } else {
      $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Aucun véhicule n'a été trouvé pour ces critères.</div>";
    }

  } else {
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire.</div>";
  }

  if (!$error) {
    $content .= "<div id='results' class='container d-flex justify-content-end pt-3'><a href='?id_agency={$_GET['id_agency']}&date_pickup={$_GET['date_pickup']}&date_return={$_GET['date_return']}&order=ascending&find_vehicle' class='mx-2 '>prix <strong>&#8593;</strong></a>";
    $content .= "<a href='?id_agency={$_GET['id_agency']}&date_pickup={$_GET['date_pickup']}&date_return={$_GET['date_return']}&order=descending&find_vehicle' class='mx-2 '>prix <strong>&#8595;</strong></a></div>";
    
    foreach ($result as $key => $value) {
      $content .= "<div class='container'><form action='' method='POST' class='mt-4'><div class='row'>";
      $content .= "<input type='hidden' id='date_pickup' name='date_pickup' value='{$_GET['date_pickup']}'>";
      $content .= "<input type='hidden' id='date_return' name='date_return' value='{$_GET['date_return']}'>";
      
      foreach ($value as $subkey => $subvalue) {
        if ($subkey == 'id_vehicle' || $subkey == 'id_agency' || $subkey == 'total_cost') {
          $content .= "<input type='hidden' id='$subkey' name='$subkey' value='$subvalue'>";
        } else if ($subkey == 'photo') {
          $content .= "<div class='col-3'>";
          $content .= "<img src=photos/vehicles/{$subvalue} class='img-fluid'>";
          $content .= "</div>";
        } else if ($subkey == 'daily_cost') {
          $total = $subvalue * $nbDays;
          $content .= "<div class='col-2'>";
          $content .= "<p class='p-2 bg-success text-white text-center rounded-pill'>$total €<p>";
          $content .= "<p class='text-center'>Prix pour $nbDays jours</p>" ;
          $content .= "<input type='hidden' id='total_cost' name='total_cost' value=$total>"; 
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
      $content .= "<p> </p><div class='col-2 container d-flex justify-content-end align-content-start flex-wrap'><input type='submit' class='rounded-pill py-2 px-4 btn btn-primary' value='Choisir'></div>";
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
      $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Une erreur s'est produite, veuillez renouveller la recherche.</div>";
    }
  } else {
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Veuillez vous identifier pour valider une commande de véhicule.</div>";
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
<?= $error ?>
<?= $content ?>

<?php
if (
  !(isset($_GET['find_vehicle']) &&
  isset($_GET['id_agency']) &&
  isset($_GET['date_pickup']) &&
  isset($_GET['date_return']))
) {
  $stmt = $conn->query("SELECT title, description, photo, daily_cost FROM vehicles GROUP BY vehicles.title ORDER BY daily_cost");
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "<div class='d-flex mt-4 flex-wrap'>";
  foreach ($result as $key => $value) {
    echo "<div class='p-2 card col-3' style='width:20rem;'>";
    echo "<img src='photos/vehicles/{$value['photo']}' class='img-fluid card_img_top' alt={$value['title']}>";
    echo "<div class='card-body'>";
    echo "<h5 class='card-title'>{$value['title']}</h5>";
    echo "<p class='card-text'>{$value['description']}</p>";
    echo "<p class='card-text'>{$value['daily_cost']} €/jour</p>";
    echo "</div></div>";
  }
  echo "</div>";
} 
?>
<img src="images/bg-image.jpg" alt="background-image" class="img-fluid">


<?php
require_once("inc/footer.php");
?>