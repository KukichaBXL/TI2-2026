<?php
# view/guestbookView.php
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI2 | Livre d'or</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <!-- HEADER -->
    <header class="site-header">
        <div class="header-logo">
            <span class="logo-circle">Fm</span>
        </div>
        <div class="header-center">
            <h1>Livre d'Or</h1>
            <p class="header-subtitle">Laissez une trace de votre passage !</p>
        </div>
        <div class="header-actions">
            <button id="toggle-theme">Mode clair</button>
            <button class="btn-admin">⚙ Administration</button>
        </div>
    </header>

<!-- Pagination (BONUS) -->

<!-- Liste des messages -->
<ul>
    <li>
        <p><strong>firstname lastname</strong></p>
        <p><em>datemessage</em></p>
        <p>message</p>
    </li>
    <!-- Autres messages -->
    <li>
        <p><strong>firstname lastname</strong></p>
        <p><em>datemessage</em></p>
        <p>message</p>
    </li>
</ul>
<!-- Pagination (BONUS) -->
<?php
// À commenter quand on a fini de tester
// echo "<h3>Nos var_dump() pour le débugage</h3>";
// echo '<p>$_POST</p>';
// var_dump($_POST);
// echo '<p>$_GET</p>';
// var_dump($_GET);
?>

<script src="js/validation.js"></script>
</body>
</html>

