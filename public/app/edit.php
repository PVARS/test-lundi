<?php

require 'common/helper.php';
require 'common/apis.php';

session_start();

if (!isset($_SESSION['auth'])) {
    header('location: login.php');
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('location: /');
}

$error = '';
$success = '';
$client = [];

if (isset($_POST['submit'])) {
    $params = [
        'nom' => $_POST['nom'] ?? null,
        'tel' => $_POST['tel'] ?? null,
        'email' => $_POST['email'] ?? null,
        'adresse' => $_POST['adresse'] ?? null,
        'code_postal' => $_POST['code_postal'] ?? null,
        'ville' => $_POST['ville'] ?? null,
    ];

    $response = call_api(API_CLIENTS . '/' . $id, 'PUT', $params);
    $response_json = json_decode($response, true);
    if ($response_json['code'] !== 200) {
        $_SESSION['error'] = "Une erreur s'est produite côté serveur";
    } else {
        $_SESSION['success'] = 'Mis à jour avec succès';
    }
}

$response = call_api(API_CLIENTS . '/' . $id, 'GET', ['fields' => 'nom,adresse,ville,tel']);
$response_json = json_decode($response, true);
if ($response_json['code'] !== 200) {
    header('location: /detail?id=' . $id);
    exit();
} else {
    $client = $response_json['datas'];
}

if (isset($_SESSION['error']) && strlen($_SESSION['error'])) {
    $error = $_SESSION['error'];
    $_SESSION['error'] = '';
}

if (isset($_SESSION['success']) && strlen($_SESSION['success'])) {
    $success = $_SESSION['success'];
    $_SESSION['success'] = '';
}

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
$cssHTML = '';
$scriptHTML = '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'layouts/head.php'?>
    </head>
    <body>
        <?php include 'layouts/header.php'?>
        <div class="container">
            <?php
            if (!empty($error)) {
                echo <<< EOF
                    <div class="alert alert-danger mt-4" role="alert">
                        $error
                    </div>
EOF;

            }
            if (!empty($success)) {
                echo <<< EOF
                    <div class="alert alert-success mt-4" role="alert">
                        $success
                    </div>
EOF;

            }
            ?>
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="name"><h5><?php echo $client['nom'] ?></h5></div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-gear"></i>
                                    Editer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <form action="<?php echo $_SERVER['SCRIPT_NAME'] . '?id=' . $id ?>" method="POST">
                        <h5 class="card-title text-center mb-3"><strong>INFORMATIONS</strong></h5>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-md-10 d-flex justify-content-center">
                                <div class="row w-100">
                                    <div class="col-md-6 text-md-end text-center">
                                        <p><strong>Prénom & NOM</strong></p>
                                        <p><strong>Téléphone</strong></p>
                                        <p><strong>Email</strong></p>
                                        <p><strong>Adresse</strong></p>
                                        <p><strong>Code postal</strong></p>
                                        <p><strong>Ville</strong></p>
                                    </div>
                                    <div class="col-md-6 text-md-start text-center">
                                        <input type="text" class="form-control" name="nom" value="<?php echo $client['nom']?>">
                                        <input type="text" class="form-control" name="tel" value="<?php echo $client['tel']?>">
                                        <input type="email" class="form-control" name="email" value="<?php echo $client['email']?>">
                                        <input type="text" class="form-control" name="adresse" value="<?php echo $client['adresse']?>">
                                        <input type="text" class="form-control" name="code_postal" value="<?php echo $client['code_postal']?>">
                                        <input type="text" class="form-control" name="ville" value="<?php echo $client['ville']?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="/detail.php?id=<?php echo $id ?>" class="btn btn-outline-secondary" style="margin-right: 5px">
                                Annuler
                            </a>
                            <input type="hidden" name="submit" value="submit">
                            <button type="submit" class="btn btn-success">Enregister</button>
                        </div>
                    </form>
                </div>
            </div


        </div>
        <?php include 'layouts/footer.php'?>
        <?php include 'layouts/script.php'?>
    </body>
</html>