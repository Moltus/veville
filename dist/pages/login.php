<?php

require_once("../inc/init.php");
//echo $_GET['connect'];

if(isset($_GET['action']) && $_GET['action'] == 'logout') {
  session_destroy();
}
if(isConnected()){
  header("Location:" . PAGES . "profile.php"); // avec . PAGES . ou sans
}

if (isset($_GET['connect']) && $_GET['connect'] == 'valid') {
  //echo "connect = valid";
  $info .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-success mt-4'>Vous êtes maintenant inscrit sur notre site. Vous pouvez dès à présent vous connecter !!</div>";
}

if ($_POST) {
  if (count($_POST) === 2){
    // $info .= "<div class='col-md-6 mx-auto text-dark text-center alert alert-success mt-4'>Formulaire correctement rempli</div>";

      // parer aux failles XSS avec strip_tags pour retirer tous les chevrons
    foreach($_POST as $key => $value) {
      $_POST[$key] = strip_tags($value);
    }
  } else {
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Merci de remplir tous les champs du formulaire</div>";
  }

  $result = $conn->prepare("SELECT * FROM users WHERE user_name = :name_email || email = :name_email");
  $result->bindValue(':name_email', $_POST['name_email'], PDO::PARAM_STR);
  $result->execute(); // on selectionne dans la BDD tous les membres qui possèdent le même pseudo ou email que l'internaute a saisi dans le formulaire

  if($result->rowcount() !== 0){ //pseudo ou email reconnu
    $user = $result->fetch(PDO::FETCH_ASSOC);

    // password_verify() est une fonction prédéfinie permettant de comparer une chaîne de caractères à une clé de hashage.
    if(password_verify($_POST['password'], $user['password'])){
      // mdp correct
      foreach ($user as $key => $value) {
        if($key != 'password'){
          $_SESSION['user'][$key] = $value;
          // on stocke les données de l'internaute directement dans son fichier session (sauf mdp). Le fichier session est donc un tableau multidimensionnel.
        }
      }
      echo '<pre>'; print_r($_SESSION); echo'</pre>';
      header("Location:" . PAGES . "profile.php"); // on le redirige ensuite vers sa page profil

    } else {
      // mauvais mdp
    }
  } else { // pseudo ou email inconnu
    $error .= "<div class='col-md-5 mx-auto text-dark text-center alert alert-danger'>Identifiant ou e-mail inexistant</div>";
  }

  $info .= $error;

}

require_once("../inc/header.php");

?>

<?php echo $info ?>
<div class="container">
  <h1 class="mt-4 text-center">Identification</h1>
  <?php echo $error ?>
  <form action="" method="POST" class="mt-4 col-md-4 offset-4">
    <div class="form-group">
      <label for="name_email">nom d'utilisateur / e-mail</label>
      <input type="text" id="name_email" name="name_email" class="form-control">
    </div>
    <div class="form-group">
      <label for="password">mot de passe</label>
      <input type="password" id="password" name="password" class="form-control">
    </div>
    <input type="submit" value="valider">
  </form>
</div>


<?php
require_once("../inc/footer.php");
?>