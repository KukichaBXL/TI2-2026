/* ============================================================================
   TRAVAIL D'INTÉGRATION JAVASCRIPT / jQuery
   Gestion d'un formulaire de contact + Dark Mode
   ============================================================================

   OBJECTIF GÉNÉRAL
   ----------------
   Créer une page contenant un formulaire de contact validé côté client en
   jQuery, avec un système de bascule entre mode clair et mode sombre.
   L'envoi final est géré par PHP qui affiche un message de retour.

   ============================================================================
   PARTIE 1 — STRUCTURE HTML À PRÉVOIR
   ============================================================================

   Vous devez créer un formulaire contenant AU MINIMUM les champs suivants :

     - Nom               (input text)
     - Prénom            (input text)
     - Email             (input email)
     - Code postal belge (input text)
     - Numéro de téléphone belge (input text)
     - Message           (textarea)
     - Bouton d'envoi    (button submit)

   Prévoir également :
     - Une zone <div id="messages"></div> en HAUT du formulaire pour afficher
       les messages d'erreur (rouge) ou de succès (vert).
     - Un bouton <button id="toggle-theme"></button> pour basculer le thème.

   ============================================================================
   PARTIE 2 — VALIDATION JAVASCRIPT (jQuery OBLIGATOIRE)
   ============================================================================

   Au clic sur le bouton d'envoi, vérifier CHAQUE champ.
   Si un champ ne respecte pas sa condition, afficher un message EN ROUGE
   en haut du formulaire, dans la zone #messages.
   Si TOUS les champs sont valides, afficher un message EN VERT et envoyer
   le formulaire (qui sera traité par PHP — voir partie 3).

   --- RÈGLES DE VALIDATION ---

   1) Nom et Prénom
      - Champs obligatoires (non vides)
      - Au moins 2 caractères

   2) Email
      - Champ obligatoire
      - Doit respecter le format d'une adresse email valide
        (utiliser une expression régulière — regex)

   3) Code postal belge
      - 4 chiffres exactement
      - Compris entre 1000 et 9999

   4) Numéro de téléphone belge
      - Doit accepter les formats suivants :
          • 0470123456
          • 0470 12 34 56
          • +32 470 12 34 56
          • 0032470123456
      - Indice : nettoyer la chaîne (enlever espaces, tirets, points)
        AVANT de tester avec une regex

   5) Message
      - Champ obligatoire
      - Au moins 10 caractères

   --- AFFICHAGE DES MESSAGES ---

   - Tous les messages d'erreur s'affichent dans la zone #messages,
     en haut du formulaire.
   - Couleur rouge pour les erreurs, couleur verte pour le succès.
   - Vider la zone à chaque nouvelle tentative d'envoi.

   ============================================================================
   PARTIE 3 — TRAITEMENT CÔTÉ PHP
   ============================================================================

   Si tous les champs sont valides, le formulaire est envoyé à un script PHP.
   Ce script doit afficher :

     - "Merci pour votre nouveau message" en VERT si l'envoi a réussi.
     - "Problème lors de l'envoi du message" en ROUGE si l'envoi a échoué.

   Note : pour cet exercice, le PHP peut simuler la réussite/échec
   (par exemple, vérifier que les variables $_POST sont bien remplies).

   ============================================================================
   PARTIE 4 — DARK MODE
   ============================================================================

   Créer un bouton qui permet de basculer entre deux thèmes :

     ☀️ Mode clair  → body avec fond BLANC
     🌙 Mode sombre → body avec fond NOIR

   COMPORTEMENT DU BOUTON :
   - Le texte du bouton change dynamiquement :
       • "🌙 Dark Mode"  quand on est en mode clair (clic = passer en sombre)
       • "☀️ White Mode" quand on est en mode sombre (clic = passer en clair)
   - L'icône doit correspondre au mode vers lequel on bascule.

   IMPLÉMENTATION SUGGÉRÉE :
   - Utiliser une classe CSS (ex : .dark-mode) sur le <body>.
   - Faire le toggle de cette classe en jQuery avec .toggleClass().
   - Mettre à jour le texte du bouton après chaque toggle.

   ============================================================================
   PARTIE 5 — BONUS
   ============================================================================

   Sur le champ "Message", limiter dynamiquement à 300 caractères MAXIMUM.

   Suggestions :
   - Utiliser l'attribut HTML maxlength="300" (rapide mais peu visuel)
   - OU mieux : afficher un compteur en temps réel sous le champ,
     du type "143 / 300 caractères", qui se met à jour à chaque frappe.
   - Bonus du bonus : passer le compteur en rouge quand il approche
     de la limite (par exemple à partir de 280 caractères).

   ============================================================================
   CRITÈRES D'ÉVALUATION
   ============================================================================

   - Utilisation correcte de jQuery (sélecteurs, événements, manipulation DOM)
   - Validation rigoureuse de tous les champs avec les bonnes regex
   - Affichage clair des messages d'erreur et de succès
   - Dark mode fonctionnel avec changement dynamique du texte/icône
   - Code propre, indenté et commenté
   - HTML sémantique et CSS soigné
   - Bonus implémenté (compteur de caractères)

   ============================================================================
   À RENDRE
   ============================================================================

   - script.js   (toute la logique jQuery)
   - traitement.php

   Bon travail !
   ========================================================================= */

// DARK MODE
$("#toggle-theme").on("click", function () {
  $("body").toggleClass("dark");
});

// TEST SOUMISSION
$("#guestbookForm").on("submit", function (e) {
  e.preventDefault();

  const errors = [];
  const isValid = true;

  // Regex
  const regexEmail = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,6}$/;
  const regexPostcode = /^\d{4}$/;
  const regexPhone = /^04\d{8}$/;

  // Récup des valeurs
  const lastname = $("#lastname").val().trim();
  const firstname = $("#firstname").val().trim();
  const usermail = $("#usermail").val().trim();
  const postcode = $("#postcode").val().trim();
  const phone = $("#phone").val().trim();
  const message = $("#message").val().trim();
  const rgpd = $("#rgpd").is(":checked");

  // Nom
  if (lastname.length < 2) {
    errors.push("Le nom doit contenir au moins 2 caractères.");
    isValid = false;
  }

  // Prénom
  if (firstname.length < 2) {
    errors.push("Le prénom doit contenir au moins 2 caractères.");
    isValid = false;
  }

  // Email
  if (usermail === "") {
    errors.push("L'adresse e-mail est vide.");
    isValid = false;
  } else if (!regexEmail.test(usermail)) {
    errors.push(
      "L'adresse e-mail n'est pas valide (ex : john.smith@example.com).",
    );
    isValid = false;
  }

  // Code postal belge
  if (!regexPostcode.test(postcode)) {
    errors.push("Le code postal doit contenir exactement 4 chiffres.");
    isValid = false;
  } else if (parseInt(postcode) < 1000 || parseInt(postcode) > 9999) {
    errors.push("Le code postal belge doit être compris entre 1000 et 9999.");
    isValid = false;
  }

  // Téléphone belge / accepte plusieurs format puis converti
  const phoneCleaned = phone.replace(/[\s\.\-]/g, ""); // retire espace et les points
  if (phoneCleaned.startsWith("+32")) {
    phoneCleaned = "0" + phoneCleaned.substring(3);
  } else if (phoneCleaned.startsWith("0032")) {
    phoneCleaned = "0" + phoneCleaned.substring(4);
  }
  if (phone === "") {
    errors.push("Le numéro de téléphone est vide.");
    isValid = false;
  } else if (!regexPhone.test(phoneCleaned)) {
    errors.push(
      "Le téléphone doit être un numéro belge valide (ex : 0470 12 34 56 ou +32 470 12 34 56).",
    );
    isValid = false;
  }

  // Message
  if (message.length < 10) {
    errors.push("Le message doit contenir au moins 10 caractères.");
    isValid = false;
  }

  // Checkbox
  if (!rgpd) {
    errors.push("Vous devez accepter le stockage de vos données personnelles.");
    isValid = false;
  }

  // AFFICHAGE des erreurs ou envoi du formulaire
  const $messages = $("#messages");
  $messages.empty(); // vide à chaque fois

  if (!isValid) {
    // Affiche toutes les erreurs en rouge
    const html = '<p class="msg-error">' + errors.join("<br>") + "</p>";
    $messages.html(html);
  } else {
    // Tout valide : message vert + envoi
    $messages.html(
      '<p class="msg-success">Formulaire valide, envoi en cours...</p>',
    );
    this.submit();
  }
});
