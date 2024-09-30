<?php

require 'common/helper.php';
require 'common/apis.php';

session_start();

if (!isset($_SESSION['auth'])) {
    header('location: login.php');
}

$params = [];
$clients = [];

if (isset($_GET['nom'])) {
    $params['nom'] = $_GET['nom'];
}
if (isset($_GET['ville'])) {
    $params['ville'] = $_GET['ville'];
}
if (isset($_GET['sort'])) {
    $params['sort'] = $_GET['sort'];
}
if (isset($_GET['fields'])) {
    $params['fields'] = $_GET['fields'];
}
if (isset($_GET['limit'])) {
    $params['limit'] = $_GET['limit'];
}

$response = call_api(API_CLIENTS, 'GET', array_merge($params, ['fields' => 'nom,adresse,ville,tel']));
$response_json = json_decode($response, true);
if ($response_json['code'] !== 200) {
    $_SESSION['error'] = "Une erreur s'est produite côté serveur";
} else {
    $clients = $response_json['datas'];
    unset($_SESSION['error']);
}

function build_sort_url($column) {
    $current_sort = $_GET['sort'] ?? '';

    if ($current_sort === $column) {
        $direction = '-' . $column;
    } else {
        $direction = $column;
    }

    $params = array_merge($_GET, ['sort' => $direction]);
    return $_SERVER['SCRIPT_NAME'] . '?' . http_build_query($params);
}

function is_sorting($column) {
    $sort = $_GET['sort'] ?? '';
    return $sort === $column || $sort === '-' . $column;
}

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'layouts/head.php'?>
        <style>
            .search-form-body {
                padding-right: 200px;
                padding-left: 200px;
            }
        </style>
    </head>
    <body>
        <?php include 'layouts/header.php'?>
        <div class="container">
            <?php
                if (!empty($_SESSION['error'])) {
                    $error = $_SESSION['error'];
                    echo <<< EOF
                    <div class="alert alert-danger mt-4" role="alert">
                        $error
                    </div>
EOF;
                }
            ?>
            <div class="card mt-4">
                <div class="card-body">
                    Recherche d'une fiche de contact
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body search-form-body">
                    <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="GET">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Renseigner un nom ou une dénomination</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom ou dénomination"
                                value="<?php echo $_GET['nom'] ?? '' ?>">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-bordered mt-3">
                <thead>
                <tr>
                    <th scope="col">
                        <a style="color: black; text-decoration: none" href="<?php echo build_sort_url('nom'); ?>">Nom</a>
                        <?php if (is_sorting('nom')): ?>
                            <i class="fa-solid fa-sort-<?php echo strpos($_GET['sort'], '-') === 0 ? 'down' : 'up'; ?>"></i>
                        <?php endif; ?>
                    </th>
                    <th scope="col">Addresse</th>
                    <th scope="col">
                        <a style="color: black; text-decoration: none" href="<?php echo build_sort_url('ville'); ?>">Ville</a>
                        <?php if (is_sorting('ville')): ?>
                            <i class="fa-solid fa-sort-<?php echo strpos($_GET['sort'], '-') === 0 ? 'down' : 'up'; ?>"></i>
                        <?php endif; ?>
                    </th>
                    <th scope="col">Téléphone</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($clients as $client) {
                        $id = $client['id'];
                        $nom = $client['nom'];
                        $addrese = $client['adresse'];
                        $ville = $client['ville'];
                        $tel = $client['tel'];
                        echo <<<EOF
                            <tr>
                                <td>$nom</td>
                                <td>$addrese</td>
                                <td>$ville</td>
                                <td>$tel</td>
                                <td>
                                    <a href="/detail.php?id=$id" class="btn btn-primary">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        Voir
                                    </a>
                                </td>
                            </tr>
EOF;

                    }
                ?>
                </tbody>
            </table>
        </div>
        <?php include 'layouts/footer.php'?>
        <?php include 'layouts/script.php'?>
    </body>
</html>