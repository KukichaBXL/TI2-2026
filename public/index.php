<?php
# public/index.php


/*
 * Front Controller de la gestion du livre d'or
 */

/*
 * Chargement des dépendances
 */
// chargement de configuration
require_once "../config.php";
// chargement du modèle de la table guestbook
require_once URL_BASE . "/model/guestbookModel.php";

/*
 * Connexion à la base de données en utilisant PDO
 * Avec un try catch pour gérer les erreurs de connexion
 * Utilisez les constantes de config.php
 * Activez le mode d'erreur de PDO à Exception et
 * le mode fetch à tableau associatif
 */

try {
    $dsn = DB_DRIVER . ":host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $db  = new PDO($dsn, DB_LOGIN, DB_PWD);
    $db->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

/*
 * Si le formulaire a été soumis
 */

// on appelle la fonction d'insertion dans la DB (addGuestbook())

// si l'insertion a réussi

// on redirige vers la page actuelle (ou on affiche un message de succès)

// sinon, on affiche un message d'erreur

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = $_POST["firstname"] ?? "";
    $lastname  = $_POST["lastname"]  ?? "";
    $usermail  = $_POST["usermail"]  ?? "";
    $phone     = $_POST["phone"]     ?? "";
    $postcode  = $_POST["postcode"]  ?? "";
    $message   = $_POST["message"]   ?? "";

    $result = addGuestbook($db, $firstname, $lastname, $usermail, $phone, $postcode, $message);

    $succes = true;
    if ($result) {
        $succes        = true;
        $messageRetour = "Merci pour votre nouveau message !";
    } else {
        $messageRetour = "Problème lors de l'envoi du message. Vérifiez vos données.";
    }
}

/*
 * On récupère les messages du livre d'or
 */

// on appelle la fonction de récupération de la DB (getAllGuestbook())

/*********************
 * Ou Bonus Pagination
 *********************/

// on vérifie sur quelle page on est (et que c'est un string qui contient que des numériques sans "." ni "-" => ctype_digit) en utilisant la variable $_GET et les constantes de config.php

# on compte le nombre total de messages (SQL)

# on récupère la pagination

# pour obtenir le $offset pour les messages (calcul)

# on veut récupérer les messages de la page courante

// Bonus pagination : vérification de la page courante avec ctype_digit
$pageGet  = $_GET[PAGINATION_GET] ?? "1";
$pageActu = (ctype_digit($pageGet) && (int)$pageGet > 0) ? (int)$pageGet : 1;

// Nombre total de messages
$nbTotal = getNbTotalGuestbook($db);

// Récupération des messages de la page courante
$entries = getGuestbookPagination($db, $pageActu, PAGINATION_NB);

// HTML de la pagination
$paginationHtml = pagination($nbTotal, "./?", PAGINATION_GET, $pageActu, PAGINATION_NB);

/**************************
 * Fin du Bonus Pagination
 **************************/

// Appel de la vue

include URL_BASE . "/view/guestbookView.php";

// fermeture de la connexion (bonne pratique)

$db = null;