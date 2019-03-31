<?php
require_once("inc/init.php");
require_once("inc/header.php");
?>

<div class="home-hero d-flex flex-column align-items-center">
  <img src="images/hero-image.jpg" alt="hero-image" class="home-hero__image">
  <h1 class="mt-4 text-center home-hero__title text-white">Véhicules + Ville = Véville</h1>
  <form action="" method="POST" class="home-hero__form">
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
        <label for="date_receipt" class="text-white">Début de location</label>
        <input type="text" size="16" class="form-control" id="date_receipt" readonly>
        <script type="text/javascript">
        $("#date_receipt").datetimepicker({
          format: 'yyyy-mm-dd hh:ii',
          autoclose: true
        });
        </script>     
      </div>
      
      <div class="form-group px-2">
        <label for="date_return" class="text-white">Fin de location</label>
        <input type="text" size="16" class="form-control" id="date_return" readonly>
        <script type="text/javascript">
        $("#date_return").datetimepicker({
          format: 'yyyy-mm-dd hh:ii',
          autoclose: true
        });
        </script>     
      </div>

      <div class="form-group px-2">
        <label for="" class="text-white">Roulez!</label>
        <input type="submit" value="Valider un véhicule" class="form-control btn btn-info">
      </div>
      
    </form>
  </div>  

</div>
<?php
require_once("inc/footer.php");
?>