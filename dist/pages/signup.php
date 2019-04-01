<?php
require_once("../inc/init.php");
require_once("../inc/header.php");
//  1. Réaliser un formulaire HTML correspondant à la table membre de la boutique (sauf id_membre et status)
//  2. Contrôler en PHP que l'on receptionne bien toutes les donnes saisies
//  3. Contrôler la disponibilité du pseudo et de l'email.

if(isConnected()){
  header("Location:" . PAGES . "profile.php"); // avec . PAGES . ou sans
}

if ($_POST){
  print_r($_POST);

  // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
  foreach($_POST as $key => $value) {
    $_POST[$key] = strip_tags($value);
    
  }

  if (count($_POST) === 7){
    echo '<strong>Formulaire correctement rempli</strong>';
  } else {
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Merci de remplir tous les champs du formulaire</div>";
  }

  $userCheck = $conn->prepare("SELECT * FROM users WHERE user_name = :user_name");
  $userCheck->bindValue(':user_name', $_POST['user_name'], PDO::PARAM_STR); 
  // $userCheck->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
  $userCheck->execute();
  $data = $userCheck->fetch(PDO::FETCH_ASSOC);
  if($data['user_name'] == $_POST['user_name']){
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Identifiant inexistant. Merci d'en saisir un nouveau ou vous <a href='connection.php' class='alert-link'>connectez</a> avec cet identifiant</div>";
  }

  $verif_email = $conn->prepare("SELECT * FROM users WHERE email = :email");
  $verif_email->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
  $verif_email->execute();
  if($data['email'] == $_POST['email']){
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Cet adresse e-mail est indisponible !! Merci d'en saisir une nouvelle ou vous <a href='connection.php' class='alert-link'>connectez</a> avec cet adresse e-mail</div>";
  }
  // verif password
  if($_POST['password'] !== $_POST['password_confirm']){
    $error .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-danger'>Vérifiez que mot-de-passe et confirmation de mot-de-passe sont bien les mêmes.</div>";
  }

  if(!$error) {
    // Réaliser la requête d'insertion à la validation du formulaire
    $result = $conn->prepare("INSERT INTO users (user_name, password, family_name, first_name, email, sex) VALUES (:user_name, :password, :family_name, :first_name, :email, :sex)");

    foreach ($_POST as $key => $value) {
      if ($key != 'password_confirm'){
        if ($key === 'password') {
          $result->bindValue(":$key", password_hash($value, PASSWORD_DEFAULT), PDO::PARAM_STR);
        } else {
          $result->bindValue(":$key", $value, PDO::PARAM_STR);
        }
      }
    }

    $result->execute();

    header("Location:" . PAGES . "login.php?connect=valid");
    // header() est une fonction prédéfinie en PHP permettant d'effectuer une redirection, dans notre car, si l'internaute a bien correctement rempli le formulaire on le redirige vers connexion.php
  }
}
?>

 
<h1 class="mt-4 text-center">Formulaire d'inscription</h1>
<?php echo $error ?>
<form action="" method="POST" class="mt-4 col-md-4 offset-4">
  <div class="form-group">
    <label for="user_name">identifiant</label>
    <input type="text" id="user_name" class="form-control" name="user_name">
  </div>
  <div class="form-group">
    <label for="password">mot de passe</label>
    <input type="password" id="password" class="form-control" name="password">
  </div>
  <div class="form-group">
    <label for="password_confirm">confirmation mot de passe</label>
    <input type="password" id="password_confirm" class="form-control" name="password_confirm">
  </div>
  <div class="form-group">
    <label for="family_name">nom</label>
    <input type="text" id="family_name" class="form-control" name="family_name">
  </div>
  <div class="form-group">
    <label for="first_name">prenom</label>
    <input type="text" class="form-control" id="first_name" name="first_name">
  </div>
  <div class="form-group">
    <label for="email">email</label>
    <input type="email" name="email" id="email" class="form-control">
  </div>
  <div class="form-group">
    <label for="sex">civilité</label>
    <select name="sex" id="sex" class="form-control">
      <option value="m">m</option>
      <option value="f">f</option>
    </select>
  </div>
  <!-- <div class="form-group">
    <label for="city">ville</ville>
    <input type="text" id="city" name="city" class="form-control">
  </div>
  <div class="form-group">
    <label for="zip-code">code postal</label>
    <input type="number" name="zip-code" id="zip-code" class="form-control">
  </div>
  <div class="form-group">
    <label for="address">adresse</label>
    <input type="text" class="form-control" id="address" name="address">
  </div> -->
  <input type="submit" value="valider">
</form>
<img src="../images/bg-image.jpg" alt="" class="img-fluid">

<?php
require_once("../inc/footer.php");
?>