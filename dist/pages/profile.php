<?php
require_once("../inc/init.php");

if(!isConnected()){
  header("Location:" . PAGES . "login.php");
}
// echo '<pre>'; print_r($_SESSION); echo '</pre>';
foreach ($_SESSION['user'] as $key => $value) {
  // echo $key, $value;
  if ($key != 'id_user' && $key != 'status'){
    $profile[$key] = $value;
  }
}
// echo "<br>infos profil : ";
// print_r($profile);

require_once("../inc/header.php");
?>

<div class="card mt-4" style="width: 18rem;">
  <img src="../images/user.png" style="width: 150px; margin: 0 auto" class="card-img-top" alt="...">
  <div class="card-body mx-auto">
    <h5 class="card-title mb-4">Infos profil de <?= $profile['user_name'] ?></h5>
    <p class="card-text">Nom : <?= $profile['family_name'] ?></p>
    <p class="card-text">Prénom : <?= $profile['first_name'] ?></p>
    <p class="card-text">E-mail : <?= $profile['email'] ?></p>
    <p class="card-text">civilité : <?= $profile['sex'] ?></p>
    
    <a href="#" class="btn btn-primary bg-dark">Modifiez les informations</a>
  </div>
</div>




<?php
require_once("../inc/footer.php");
?>