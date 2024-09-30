<?php

require 'common/helper.php';
require 'common/apis.php';

session_start();

if (isset($_SESSION['auth'])) {
    header('location: /');
}

$error = false;

if (isset($_POST['submit'])) {
    $params = [
        'username' => $_POST["username"] ?? null,
        'password' => $_POST["password"] ?? null,
        'password_type' => 0,
        'code_application' => 'webservice_externe',
        'code_version' => 1
    ];

    $response = call_api(API_LOGIN, 'POST', $params);
    $response_json = json_decode($response, true);
    if ($response_json['code'] !== 200) {
        $_SESSION['error'] = "Veuillez entrer votre nom d'utilisateur ou votre mot de passe";
    } else {
        $_SESSION['auth'] = $response_json['datas'];
        header('location: /');
        exit();
    }
}

if (isset($_SESSION['error']) && strlen($_SESSION['error'])) {
    $error = $_SESSION['error'];
    $_SESSION['error'] = '';
}

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------

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
    ?>
    <div class="card mt-3">
        <div class="card-header text-center">
            <h3>Login</h3>
        </div>
        <div class="card-body">
            <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $_POST["username"] ?? '' ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" value="">
                </div>
                <input type="hidden" name="submit" value="submit">
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include 'layouts/footer.php'?>
<?php include 'layouts/script.php'?>
</body>
</html>