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
    <script src="js/jquery-3.7.1.min.js"></script>
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
            <button id="toggle-theme">🌙 Dark Mode</button>
            <button class="btn-admin">⚙ Administration</button>
        </div>
    </header>

    <!-- CONTENT  -->

    <main>
        <div class="main-wrapper">

            <!-- COLONNE GAUCHE -->
            <aside class="form-side">
            <img src="img/dogmeme.jpg" alt="Livre d'or illustration" class="book-illustration">

            <div class="form-card">
                <h2>Votre message</h2>

            <!-- Zone messages JS (erreurs/succès jQuery) -->
            <div id="messages">
                <?php if (!empty($messageRetour)) : ?>
                    <p class="
                    <?= $succes ? 'msg-success' : 'msg-error' ?>">
                     <?= $messageRetour ?>
                    </p>
                <?php endif; ?>
            </div>


            <!--  -->
            <form method="POST" action="" id="guestbookForm">

            <div class="form-group">
                <label for="lastname">Nom</label>
                <input type="text" id="lastname" name="lastname" placeholder="Ex: Smith"
                value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" require>
            </div>

            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input type="text" id="firstname" name="firstname" placeholder="Ex: John"
                value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" require>
            </div>

            <div class="form-group">
                <label for="usermail">E-mail</label>
                <input type="email" id="usermail" name="usermail" placeholder="john.smith@example.com"
                value="<?= htmlspecialchars($_POST['usermail'] ?? '') ?>" require>
            </div>

            <div class="form-group">
                <label for="postcode">Code Postal</label>
                <input type="text" id="postcode" name="postcode" placeholder="Ex: 1000" maxlength="4"
                value="<?= htmlspecialchars($_POST['postcode'] ?? '') ?>" require>
                </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" id="phone" name="phone" placeholder="Ex: 04 23 45 67 89"
                value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" require>
            </div>

            <div class="form-group form-group--textarea">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Un petit mot..." maxlength="300"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <p class="char-count"><span id="charCount">0</span> / 300 caractères</p>

            <label>
            <input type="checkbox" id="rgpd" name="rgpd">
            J'accepte le stockage de mes données personnelles.
            </label>

            <button type="submit" class="btn-submit">Envoyer le message</button>

            </form>
            </div>
            </aside>

            <!-- COLONNE DROITE -->
            <section class="messages-side">
                <h2 class="messages-title">
                    Messages récents -
                    <?php if ($nbTotal === 0) : ?>
                        pas encore de message
                    <?php elseif ($nbTotal === 1) : ?>
                        il y a actuellement 1 message
                    <?php else : ?>
                        il y a actuellement <?= $nbTotal ?> messages
                    <?php endif; ?>
                </h2>

            <!-- Liste des messages -->
            <?php foreach ($entries as $entry) : ?>
                <article class="message-card">
                <div class="message-card_header">

                <div class="message-card_info">

                <span class="message-card_name">
                <?= htmlspecialchars($entry['firstname']) ?>
                <?= htmlspecialchars($entry['lastname']) ?>
                </span>

                <span class="message-card_email">
                <?= htmlspecialchars($entry['usermail']) ?>
                </span>
                </div>
                            
                <span class="message-card_date">
                <?php
                $date = new DateTime($entry['datemessage']);
                $mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
                echo $date->format('d') . ' ' . $mois[(int)$date->format('n')-1] . ' ' . $date->format('Y') . ' à ' . $date->format('H\hi');
                ?>
                </span>
                </div>

                <hr class="message-card_separator">
                <p class="message-card_text"><?= nl2br(htmlspecialchars($entry['message'])) ?></p>
                </article>

                <?php endforeach; ?>

                <!-- PAGINATION -->
                <div class="pagi">
                    <p>
                        <a href=""><?= $paginationHtml ?></a>
                    </p></div>
                

            </section>
        </div>
    </main>


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

