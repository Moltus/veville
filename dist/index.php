<?php
require_once("inc/init.php");
require_once("inc/header.php");

if (
  isset($_GET['find_vehicle']) &&
  isset($_GET['id_agency']) && $_GET['id_agency'] != "" &&
  isset($_GET['date_pickup']) && $_GET['date_pickup'] != "" &&
  isset($_GET['date_return']) && $_GET['date_return'] != ""
  ) {
    $id_agency = $_GET['id_agency'];
    echo "dates pickup et return : " . $_GET['date_pickup'] . " , " . $_GET['date_return'];
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
      print_r($value);
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
          $content .= "<input type='text' id='total_cost' name='total_cost' value='$total €' disabled class='form-control'>"; 
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
?>

<div class="home-hero d-flex flex-column align-items-center">
  <img src="images/hero-image.jpg" alt="hero-image" class="home-hero__image">
  <h1 class="mt-4 text-center home-hero__title text-white">Véhicules + Ville = Véville</h1>
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


<?php
require_once("inc/footer.php");
?>