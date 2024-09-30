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

$params = [];
$client = [];

if (isset($_GET['fields'])) {
    $params['fields'] = $_GET['fields'];
}

$response = call_api(API_CLIENTS . '/' . $id, 'GET', array_merge($params, ['fields' => 'nom,adresse,ville,tel']));
$response_json = json_decode($response, true);
if ($response_json['code'] !== 200) {
    header('location: /');
    exit();
} else {
    $client = $response_json['datas'];
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
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="name"><h5><?php echo $client['nom'] ?></h5></div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <a href="/edit.php?id=<?php echo $id ?>" class="btn btn-primary">
                                    <i class="fa-solid fa-gear"></i>
                                    Editer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
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
                                </div>
                                <div class="col-md-6 text-md-start text-center">
                                    <p><?php echo $client['nom'] ?></p>
                                    <p><?php echo $client['tel'] ?></p>
                                    <p><?php echo $client['email'] ?></p>
                                    <p><?php echo $client['adresse'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'layouts/footer.php'?>
        <?php include 'layouts/script.php'?>
    </body>
</html>