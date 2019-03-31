<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Véville locations véhicules</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <style>
    .table-fix-head th { background-color: #eee; border-top: #dee2e6; border-bottom: #dee2e6; position: sticky; top: 0; }
    </style>
  </head>
  <body>
     <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <a class="navbar-brand" href="<?=ROOT?>index.php">Véville</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample04">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="<?=ROOT?>index.php">Home<span class="sr-only">(current)</span></a>
          </li>

          <?php if(isConnected()): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?=PAGES?>login.php?action=logout">Deconnexion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=PAGES?>profile.php">Profil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=PAGES?>orders.php">Commandes</a>
          </li>

          <?php else: ?>

          <li class="nav-item">
            <a class="nav-link" href="<?=PAGES?>signup.php">Inscription</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=PAGES?>login.php?action=login">Connexion</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="/pages//panier.php">Panier</a>
          </li> -->
          <?php endif; ?>

          <?php if(isConnectedAsAdmin()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Back Office</a>
            <div class="dropdown-menu" aria-labelledby="dropdown04">
              <a class="dropdown-item" href="<?=PAGES?>admin/backoffice-vehicles.php">Gestion vehicules</a>
              <a class="dropdown-item" href="<?=PAGES?>admin/backoffice-agencies.php">Gestion agences</a>
              <a class="dropdown-item" href="<?=PAGES?>admin/backoffice-users.php">Gestion utilisateurs</a>
            </div>
          </li>
          <?php endif; ?>
        </ul>
        <form class="form-inline my-2 my-md-0">
          <input class="form-control" type="text" placeholder="Search">
        </form>
      </div>
    </nav>

    <section class="container pb-4">