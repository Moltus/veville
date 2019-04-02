<?php
require_once("../../inc/init.php");
require_once("../../inc/header.php");


if (!isConnectedAsAdmin()) {
  header("Location: pages/connexion.php");
}

// to remove users
if (isset($_GET['action']) && $_GET['action'] == 'remove'){
  // requete de suppression
  $result = $conn->prepare("DELETE FROM users WHERE id_user = :id_user");
  $result->bindValue(':id_user', $_GET['id_user'], PDO::PARAM_INT);
  $result->execute();

  $info .= "<div class='col-md-6 offset-md-3 alert alert-success text-center'>Le membre n° <strong>$_GET[id_user]</strong> a bien été supprimé.</div>";
}

// to modify users
if (isset($_GET['action']) && $_GET['action'] == 'modify') {
  // verif sécu
  if (isset($_GET['id_user'])) {
    $result = $conn->prepare("SELECT * FROM users WHERE id_user = :id_user");
    $result->bindValue(':id_user', $_GET['id_user']);
    $result->execute();

    $this_user = $result->fetch(PDO::FETCH_ASSOC);
    // print_r($this_user);
  }

  $user_id = (isset($this_user['id_user'])) ? $this_user['id_user'] : '';
} 

if ($_POST & !empty($_POST)){
  // print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach ($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (
    (isset($_POST['user_name']) && $_POST['user_name'] != "") &&
    (isset($_POST['password']) && $_POST['password'] != "") &&
    (isset($_POST['family_name']) && $_POST['family_name'] != "") &&
    (isset($_POST['first_name']) && $_POST['first_name'] != "") &&
    (isset($_POST['email']) && $_POST['email'] != "") &&
    (isset($_POST['sex']) && $_POST['sex'] != "") &&
    (isset($_POST['status']) && $_POST['status'] != "")
    ) {
      $info .= "<div class='col-md-6 mx-auto alert alert-success text-center'>Le membre <strong>" . $_POST['user_name'] . '</strong> a bien été ajouté !!</div>';
  } else if (isset($_POST['modify']) &&
    (isset($_POST['user_name']) && $_POST['user_name'] != "") &&
    (isset($_POST['family_name']) && $_POST['family_name'] != "") &&
    (isset($_POST['first_name']) && $_POST['first_name'] != "") &&
    (isset($_POST['email']) && $_POST['email'] != "") &&
    (isset($_POST['sex']) && $_POST['sex'] != "") &&
    (isset($_POST['status']) && $_POST['status'] != "")
    ){
      $info .= "<div class='col-md-6 mx-auto alert alert-warning text-center'>Le membre <strong>" . $_POST['user_name'] . '</strong> a bien été modifié !!</div>';
  } else {
      $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Merci de bien remplir tous les champs du formulaire</div>";
  } 

  if (!$error) {
    // modification statement
    if (isset($_POST['modify'])) {
      $result = $conn->prepare("UPDATE users SET user_name = :user_name, family_name = :family_name, first_name = :first_name, email = :email, sex = :sex, status = :status WHERE id_user = :id_user");

      foreach ($_POST as $key => $value) {
        if ($key != 'modify' && $key != 'password') {    
          $result->bindValue(":$key", $value, PDO::PARAM_STR);   
        }
      }

    } else {
      $result = $conn->prepare("INSERT INTO users (user_name, password, family_name, first_name, email, sex, status) VALUES (:user_name, :password, :family_name, :first_name, :email, :sex, :status)");
      
      foreach ($_POST as $key => $value) {  
        if ($key === 'password') {
          $result->bindValue(":$key", password_hash($value, PASSWORD_DEFAULT), PDO::PARAM_STR);
        } else {
          $result->bindValue(":$key", $value, PDO::PARAM_STR);      
        }
      }
    }


    $result->execute();

  }
}
?>

<!-- HTML -->
<section class="container pb-4">

<h2 class="m-4 text-center">Ajout/modification de membres</h2>

<?php echo $info ?>
<?php echo $error ?>
  
<!-- vehicles table content into HTML table -->
<div id="table-container" style="width: 1140px; height: 400px" class="mb-4 overflow-auto table-fix-head">
<?php 
$content = "";

$stmt = $conn->query("SELECT * FROM users");
$result =  $stmt->fetchAll(PDO::FETCH_ASSOC);

$content .= '<table class="table"><tr>';
$content .= "<th>id_membre</th><th>Pseudo</th><th>Nom</th><th>Prénom</th><th>E-mail</th><th>Civilité</th><th>Status</th><th>Date d'enregistrement</th>";

$content .= '<th>Modifier</th>';
$content .= '<th>Supprimer</th>';
$content .= '</tr>';
foreach ($result as $key => $value) {
  $content .= '<tr>';
  foreach ($value as $subkey => $subvalue) {
    // $content .= '<pre>'; print_r($subkey); $content .= '</pre>';
    if ($subkey != 'password') {
      $content .= "<td>$subvalue</td>";
    }
  }
  $content .= '<td><a href="?action=modify&id_user=' . $value['id_user'] . '">X</a></td>';
  $content .= '<td><a href="?action=remove&id_user=' . $value['id_user'] . '">X</a></td>';
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
          <label for="user_name">Pseudo</label>
          <input type="text" id="user_name" name="user_name" <?= 'value="', (isset($this_user)) ? $this_user['user_name'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" <?= 'value="', (isset($this_user)) ? $this_user['password'] : '', '"' ?> 
          <?= (isset($_GET['action']) && $_GET['action'] == 'modify') ? " disabled" : "" ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="family_name">Nom de famille</label>
          <input type="text" id="family_name" name="family_name" <?= 'value="', (isset($this_user)) ? $this_user['family_name'] : '', '"' ?> class="form-control">
        </div>
   
      </div>
      <div class="col-6">
      <div class="form-group">
          <label for="first_name">Prénom</label>
          <input type="text" id="first_name" name="first_name" <?= 'value="', (isset($this_user)) ? $this_user['first_name'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="address">E-mail</label>
          <input type="email" id="email" name="email" <?= 'value="', (isset($this_user)) ? $this_user['email'] : '', '"' ?> class="form-control">
        </div>
        <div class="form-group">
          <label for="sex">Civilité</label>
          <select name="sex" id="sex">
            <option value="m" <?= (isset($this_user) && $this_user['sex'] == 'm') ? "selected" : "" ?>>Homme</option>
            <option value="f" <?= (isset($this_user) && $this_user['sex'] == 'f') ? "selected" : "" ?>>Femme</option>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Statut</label>
          <select name="status" id="status">
            <option value="0" <?= (isset($this_user) && $this_user['status'] == '0') ? "selected" : "" ?>>Régulier</option>
            <option value="1" <?= (isset($this_user) && $this_user['status'] == '1') ? "selected" : "" ?>>Admin</option>
          </select>
        </div>
        
          
        <!-- submits inputs -->
        <?php 
        $content = "";
        if (isset($_GET['action']) && $_GET['action'] == 'modify') {
          // user modification inputs
          $content .= '<div class="form-group row">';
          $content .= '<label for="id_user" class="col-sm-3 col-form-label">Membre n°</label>';
          $content .= '<input id="id_user" name="id_user" type="text" readonly class="form-control col-2" value="' . $_GET['id_user'] . '">';
          $content .= '<input id="modify-btn" class="btn btn-warning ml-2" type="submit" value="Modifier" name="modify">';
          $content .= '<a  class="btn btn-info ml-2" href="javascript:window.location = window.location.href.split(' . "'?'" . ')[0]">Annuler</a></div>';

          echo $content;
        } else {
          // submit user input
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