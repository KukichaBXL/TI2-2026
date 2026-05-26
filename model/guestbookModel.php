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

    // Validation téléphone belge : nettoyer puis vérifier format 04xxxxxxxx
    $phoneCleaned = preg_replace('/[\s\.\-]/', '', $phone); // retire espaces, points, tirets
    if (str_starts_with($phoneCleaned, '+32')) {
        $phoneCleaned = '0' . substr($phoneCleaned, 3);
    } elseif (str_starts_with($phoneCleaned, '0032')) {
        $phoneCleaned = '0' . substr($phoneCleaned, 4);
    }
    if (!preg_match('/^04\d{8}$/', $phoneCleaned)) return false;

    // requête préparée obligatoire !
    try {
        $sql  = "INSERT INTO guestbook (firstname, lastname, usermail, phone, postcode, message)
                 VALUES (:firstname, :lastname, :usermail, :phone, :postcode, :message)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':usermail'  => $usermail,
            ':phone'     => $phoneCleaned,
            ':postcode'  => $postcode,
            ':message'   => $message,
        ]);
        return true;
    } catch (PDOException $e) {
        die("Erreur SQL addGuestbook : " . $e->getMessage());
    }

    // si l'insertion a réussi
    // on renvoie true
    // sinon, on renvoie false

}

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
    // try catch
    // si la requête a réussi,
    // bonne pratique, fermez le curseur
    // renvoyer le tableau de(s) message(s)
    try {
        $stmt   = $db->query("SELECT * FROM guestbook ORDER BY datemessage DESC");
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL getAllGuestbook : " . $e->getMessage());
    }
    return [];
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
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM guestbook");
        $nb   = (int) $stmt->fetchColumn();
        $stmt->closeCursor();
        return $nb;
    } catch (PDOException $e) {
        die("Erreur SQL getNbTotalGuestbook : " . $e->getMessage());
    }
    // bonne pratique, fermez le curseur,
    // renvoyez le nombre total de messages
    return 0;

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

     try {
        $offset = ($pageActu - 1) * $limit;
        $sql    = "SELECT * FROM guestbook ORDER BY datemessage DESC LIMIT :limit OFFSET :offset";
        $stmt   = $db->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL getGuestbookPagination : " . $e->getMessage());
    }
    // Requête préparée obligatoire !
    // Le $offset et le $limit sont des entiers, il faut donc les passer
    // en paramètres de la requête préparée en tant qu'entiers !
    // si la requête a réussi,
    // bonne pratique, fermez le curseur
    // renvoyer le tableau de(s) message(s) (vide si pas de résultats)
    return [];
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