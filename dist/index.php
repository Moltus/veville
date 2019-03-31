<?php
require_once("inc/init.php");
require_once("inc/header.php");
?>

<div class="home-hero">
  <img src="images/hero-image.jpg" alt="hero-image" class="home-hero__image">
  <h1 class="mt-4 text-center home-hero__title">Véhicules + Ville = Véville</h1>
  <form action="" method="POST" class="home-hero__form">
    <div class="row">
      <div class="form-group">
        <label for="id_agency">Agence de départ</label>
        <select name="id_agency" id="id_agency">
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
      <div class="form-group">
        <label for="date_receipt">Début de location</label>
        <input type="datetime" name="date_receipt" id="date_receipt"
        <?= 'value="', (isset($this_vehicle)) ? $this_vehicle['title'] : '', '"' ?> class="form-control">
      </div>
    </div>
  </form>
</div>  
<div class="container">
</div>

<?php
require_once("inc/footer.php");
?>