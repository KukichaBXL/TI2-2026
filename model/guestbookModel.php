<?php
# model/guestbookModel.php
/********************************
 * Model de la page livre d'or
 *******************************/

// INSERTION d'un message dans le livre d'or

/**
 * @param PDO $db
 * @param string $firstname
 * @param string $lastname
 * @param string $usermail
 * @param string $phone
 * @param string $postcode
 * @param string $message
 * @return bool
 * Fonction qui insère un message dans la base de données 'ti2web2026' et sa table 'guestbook'
 * Renvoie true si l'insertion a réussi, false sinon
 * Une requête préparée est utilisée pour éviter les injections SQL
 * Les données sont échappées pour éviter les injections XSS (protection backend)
 */
function addGuestbook(PDO $db,
                    string $firstname,
                    string $lastname,
                    string $usermail,
                    string $phone,
                    string $postcode,
                    string $message
): bool {
    // traitement des données backend (SECURITE)
    $firstname = htmlspecialchars(strip_tags(trim($firstname)));
    $lastname  = htmlspecialchars(strip_tags(trim($lastname)));
    $usermail  = htmlspecialchars(strip_tags(trim($usermail)));
    $phone     = htmlspecialchars(strip_tags(trim($phone)));
    $postcode  = htmlspecialchars(strip_tags(trim($postcode)));
    $message   = htmlspecialchars(strip_tags(trim($message)));
    // si pas de données complètes ou ne correspondant pas à nos attentes, on renvoie false
    
    if (empty($firstname) || strlen($firstname) < 2 || strlen($firstname) > 100) return false;
    if (empty($lastname)  || strlen($lastname)  < 2 || strlen($lastname)  > 100) return false;
    if (empty($usermail)  || strlen($usermail)  > 200 || !filter_var($usermail, FILTER_VALIDATE_EMAIL)) return false;
    if (empty($message)   || strlen($message)   < 10  || strlen($message)  > 500) return false;

    // Validation code postal belge : 4 chiffres entre 1000 et 9999
    if (!preg_match('/^\d{4}$/', $postcode)) return false;
    $postcodeInt = (int)$postcode;
    if ($postcodeInt < 1000 || $postcodeInt > 9999) return false;

    if (!preg_match('/^(\+32|0032|0)4\d{8}$/', $phone)) return false;

    $prepare = $db->prepare("
        INSERT INTO guestbook (firstname, lastname, usermail, phone, postcode, message)
        VALUES (:firstname, :lastname, :usermail, :phone, :postcode, :message)
    ");
    $prepare->bindValue(':firstname', $firstname);
    $prepare->bindValue(':lastname',  $lastname);
    $prepare->bindValue(':usermail',  $usermail);
    $prepare->bindValue(':phone',     $phone);
    $prepare->bindValue(':postcode',  $postcode);
    $prepare->bindValue(':message',   $message);
    $retour = $prepare->execute();
    return $retour;
}

    // si l'insertion a réussi 
    // on renvoie true
    // sinon, on renvoie false


/***************************
 * Sans le Bonus Pagination
 **************************/

// SELECTION de messages dans le livre d'or par ordre de date croissante
/**
 * @param PDO $db
 * @return array
 * Fonction qui récupère tous les messages du livre d'or par ordre de date croissante
 * venant de la base de données 'ti2web2026' et de la table 'guestbook'
 * Si pas de message, renvoie un tableau vide
 */
function getAllGuestbook(PDO $db): array
{
    $stmt   = $db->query("SELECT * FROM guestbook ORDER BY datemessage DESC");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $result;
}

/**************************
 * Pour le Bonus Pagination
 **************************/

// SELECTION du nombre total de messages
/**
 * @param PDO $db
 * @return int
 * Fonction qui compte le nombre total de messages dans la table 'guestbook'
 */
function getNbTotalGuestbook(PDO $db): int
{
    $stmt = $db->query("SELECT COUNT(*) AS count FROM guestbook");
    return (int) $stmt->fetch()['count'];
}
// SELECTION de messages dans le livre d'or par ordre de date croissante
// en lien avec la pagination
/**
 * @param PDO $db
 * @param int $pageActu = 1
 * @param int $limit = 5
 * @return array
 * Fonction qui récupère les messages du livre d'or par ordre de date croissante
 * venant de la base de données 'ti2web2026' et de la table 'guestbook'
 * en utilisant une requête préparée (injection SQL), n'affiche que les messages
 * de la page courante
 */
function getGuestbookPagination(PDO $db, int $pageActu=1, int $limit=5): array
{
    $offset = ($pageActu - 1) * $limit;
    $sql    = "SELECT * FROM guestbook ORDER BY datemessage DESC LIMIT :offset, :limit";
    $stmt   = $db->prepare($sql);
    $stmt->bindValue("offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue("limit",  $limit,  PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $stmt->closeCursor();
    return $result;
}

# Pour afficher la pagination dans la vue
// FONCTION de pagination
/**
 * @param int $nbtotalMessage
 * @param string $url
 * @param string $get
 * @param int $pageActu
 * @param int $perPage
 * @return string
 * Fonction qui génère le code HTML de la pagination
 * si le nombre de pages est supérieur à une.
 */
function pagination(int $nbtotalMessage, string $url="./?", string $get="page", int $pageActu=1, int $perPage=5 ): string
{
    $sortie = "";
    if ($nbtotalMessage === 0) return "";
    $nbPages = ceil($nbtotalMessage / $perPage);
    if ($nbPages == 1) return "";
    $sortie .= "<p>";
    for ($i = 1; $i <= $nbPages; $i++) {
        if ($i === 1) {
            if ($pageActu === 1) {
                $sortie .= "<< < 1 |";
            } elseif ($pageActu === 2) {
                $sortie .= " <a href='$url'><<</a> <a href='$url'><</a> <a href='$url'>1</a> |";
            } else {
                $sortie .= " <a href='$url'><<</a> <a href='$url&$get=" . ($pageActu - 1) . "'><</a> <a href='$url'>1</a> |";
            }
        } elseif ($i < $nbPages) {
            if ($i === $pageActu) {
                $sortie .= "  $i |";
            } else {
                $sortie .= "  <a href='$url&$get=$i'>$i</a> |";
            }
        } else {
            if ($pageActu >= $nbPages) {
                $sortie .= "  $nbPages > >>";
            } else {
                $sortie .= "  <a href='$url&$get=$nbPages'>$nbPages</a> <a href='$url&$get=" . ($pageActu + 1) . "'>></a> <a href='$url&$get=$nbPages'>>></a>";
            }
        }
    }
    $sortie .= "</p>";
    return $sortie;

}