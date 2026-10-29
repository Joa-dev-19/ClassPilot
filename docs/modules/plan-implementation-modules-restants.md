# Plan d’implémentation — Modules restants de ClassPilot

## 1. Identification

- Version : 1.0
- Date de mise à jour : 29 octobre 2026
- Statut : M1 et M2 préparés pour le contrôle de cours ; validation finale par le groupe à faire
- Validateur métier : à désigner
- Référence d’architecture : document « Architecture globale du projet ClassPilot » fourni pour ce plan

Les mentions « présent dans le dépôt », « à implémenter », « testé » et « validé » désignent des états distincts. La présence de code ne prouve pas à elle seule que le comportement est complet ou accepté.

## 2. Contexte et objectifs

Le module M1 — Comptes et accès — existe déjà et avait été indiqué comme réalisé. À la demande de l’utilisateur, il est révisé avant la poursuite des autres modules : francisation, inscription autonome des étudiants, vérification de l’adresse e-mail, authentification et préparation SMTP. Il s’agit d’une mise à niveau du module existant, pas d’une reconstruction.

Le dépôt contient déjà des éléments des modules M2 — Tâches scolaires — et M3 — Calendrier et rappels. Le présent plan commence donc par vérifier et compléter ces éléments, plutôt que de supposer ces modules terminés. Les modules M4 — Devoirs enseignants — et M5 — Administration — restent à cadrer puis à réaliser.

Objectif global : terminer les parcours fonctionnels restants en respectant l’architecture existante, les droits par rôle, la confidentialité des données et des critères de validation vérifiables.

Pour le contrôle demandé par le professeur le 29 octobre 2026, le périmètre démontré est M1 — Comptes et authentification — et M2 — Tâches scolaires. Ce contrôle exige au moins deux modules fonctionnels ; il n’impose pas à lui seul un CRUD complet des tâches. Le présent plan distingue donc les critères de cette évaluation des évolutions éventuelles hors périmètre.

Plans détaillés des modules retenus :

- [Plan d’implémentation M1 — Comptes et authentification](./plan-implementation-module-1-authentification.md)
- [Plan d’implémentation M2 — Tâches scolaires](./plan-implementation-module-2-taches.md)

## 3. Périmètre

### Inclus

- **M2 — Tâches scolaires :** terminer les opérations de gestion des tâches et leurs tests.
- **M3 — Calendrier et rappels :** valider le calendrier existant, fiabiliser les rappels et intégrer les échéances de devoirs après la réalisation de M4.
- **M4 — Devoirs enseignants :** permettre aux enseignants autorisés de publier des devoirs et aux utilisateurs ciblés de les consulter.
- **M5 — Administration :** permettre à un administrateur autorisé de consulter et gérer les comptes dans les limites qui seront validées.
- **M1 — Comptes et accès :** réviser l’implémentation existante, sans la reconstruire.
- **Recette intégrée :** vérifier les dépendances entre comptes, tâches, calendrier, devoirs et administration.

### Exclus

- Reconstruire de zéro l’inscription, la connexion ou la gestion de profil déjà présentes dans M1.
- Ajouter une application monopage, une API séparée ou un framework frontend sans décision explicite.
- Ajouter des classes, groupes, établissements ou inscriptions scolaires avant validation de leur modèle métier.
- Déployer l’application ou modifier l’infrastructure de production.

## 4. Existant, stack et contraintes

### Éléments repérés dans le dépôt

- M1 : le dépôt contient l’authentification Laravel, les rôles `etudiant`, `enseignant`, `administrateur`, un statut de compte et un middleware de rôle. Inscription et connexion autonomes des étudiants sont déjà présentes ; la vérification obligatoire de l’adresse e-mail et la francisation complète sont en cours de mise à niveau. Les identifiants Gmail de production restent à fournir par variables d’environnement.
- M2 : `Tache`, sa migration, une liste et un formulaire de création sont présents. Le contrôleur et les routes consultés couvrent la liste et la création ; les parcours de modification et de suppression demandés par l’architecture ne sont pas repérés dans ces routes.
- M3 : une page calendrier, le repérage des échéances à venir et en retard, la création et l’annulation de rappels, une commande d’envoi par courriel et sa planification sont présents. Leur fonctionnement opérationnel et leurs cas d’échec restent à vérifier.
- M4 : aucun contrôleur, modèle, route ou écran de devoir n’a été repéré dans les emplacements applicatifs examinés.
- M5 : aucun parcours d’administration des comptes n’a été repéré dans les routes, contrôleurs et vues examinés. Le modèle utilisateur contient déjà des rôles et un statut actif/inactif.

Ces constats sont un inventaire initial, pas une recette. Chaque étape devra confirmer l’état réel du code et des tests avant de le modifier.

### Contraintes techniques constatées

- PHP 8.4 et Laravel 13 sont disponibles dans l’environnement du projet.
- L’application existante utilise Laravel, des vues Blade et Vite. Les fichiers du projet doivent primer sur les exemples Vue/PrimeVue du guide fourni.
- Le document d’architecture propose Bootstrap 5, tandis que les écrans Blade existants utilisent les composants et styles déjà présents dans le dépôt. Ne pas introduire un nouveau système d’interface sans décision.
- La configuration de base de données cible les migrations et les tests : confirmer la base utilisée pour les validations avant de conclure qu’une contrainte est compatible.
- Réutiliser l’authentification, le middleware de compte actif, les conventions de routes et les composants existants.

## 5. Règles métier et autorisations

- **R01 — Identité :** chaque opération privée exige un utilisateur authentifié et actif.
- **R02 — Rôles :** les autorisations sont contrôlées côté serveur pour chaque action ; masquer un bouton dans l’interface ne remplace pas ce contrôle.
- **R03 — Propriété des tâches :** un étudiant ne consulte et ne modifie que ses propres tâches.
- **R04 — Données de tâche :** le titre est obligatoire ; priorité et statut doivent appartenir aux valeurs autorisées par le projet.
- **R05 — Échéances :** une tâche peut ne pas avoir de date limite, conformément au schéma existant ; confirmer les règles de date avant de les rendre plus restrictives.
- **R06 — Rappels :** un rappel appartient à une tâche accessible à l’utilisateur ; il doit être programmé avant l’échéance et ne doit pas être envoyé plusieurs fois en cas de nouvelle exécution du traitement.
- **R07 — Tâches terminées :** les rappels d’une tâche terminée ne sont pas envoyés.
- **R08 — Auteur d’un devoir :** seul un enseignant autorisé peut publier un devoir ; l’auteur est dérivé de la session côté serveur.
- **R09 — Ciblage :** la consultation d’un devoir est limitée aux destinataires définis par la décision de ciblage validée. Aucune audience ne doit être inventée dans le code.
- **R10 — Édition d’un devoir :** un enseignant ne modifie ou ne retire que les devoirs pour lesquels il dispose du droit correspondant.
- **R11 — Administration :** les actions de gestion des comptes sont réservées à un administrateur autorisé et les secrets, notamment les mots de passe, ne sont jamais affichés.
- **R12 — Statut d’un compte :** les comptes désactivés ne doivent pas conserver un accès normal aux espaces privés ; les effets sur une session déjà ouverte devront être vérifiés.
- **R13 — Historique :** ne supprimer ni modifier des données ou événements historiques sans règle métier et décision explicites.
- **R14 — Validation :** les entrées sont validées côté serveur ; les erreurs n’exposent pas d’informations techniques ou confidentielles.
- **R15 — Accessibilité :** formulaires utilisables au clavier, libellés explicites, erreurs compréhensibles et informations non transmises par la couleur seule. Viser les critères WCAG 2.2 AA de l’architecture et consigner les vérifications effectivement réalisées.

## 6. Données, interfaces et intégrations

- Réutiliser `users`, `taches` et `rappels` tels qu’ils existent, après vérification des migrations et de leurs contraintes.
- Le modèle `Devoir` et ses champs restent à concevoir après décision sur l’audience, les statuts de publication et la modification/retrait. Les champs proposés par l’architecture — auteur, titre, consignes, échéance, statut — sont une base de discussion, pas un schéma déjà existant.
- L’administration s’appuie d’abord sur le modèle `User` et son statut ; un journal d’actions est une option à décider, pas une obligation déjà approuvée.
- Suivre les routes web et vues Blade existantes. Aucun contrat d’API REST n’est imposé par le dépôt ou par ce plan.
- M3 ne pourra afficher les échéances des devoirs que lorsque la règle d’accès aux devoirs et l’intégration avec M4 seront validées.

## 7. Qualité, risques et vérifications

- Tests automatisés ciblés sur les droits, la validation, la propriété des données, les échéances et les cas de répétition.
- Recette manuelle des parcours par rôle, y compris les réponses d’accès refusé et les listes vides.
- Vérification clavier et affichage responsive des écrans ajoutés ou modifiés.
- Vérification de la configuration réelle d’envoi de courriels et du déclenchement du Scheduler dans l’environnement prévu ; les tests utilisant des notifications simulées ne prouvent pas la livraison d’un courriel réel.
- Vérification des migrations sur la base de test retenue et conservation d’une procédure de retour arrière.
- Aucun bilan ne doit annoncer un test, une conformité ou une livraison qui n’a pas été réellement vérifié.

## 8. Questions ouvertes et décisions à consigner

| Question | Impact | Décideur attendu | Décision |
|---|---|---|---|
| Les devoirs sont-ils ciblés par classe, groupe, établissement ou accessibles à tous les étudiants ? Le modèle actuel des utilisateurs ne montre pas d’association de ce type. | Bloque le modèle de données, les autorisations et la visibilité des devoirs. | Responsable métier / enseignant | À décider avant l’étape M4-1 |
| Quels sont les états d’un devoir (brouillon, publié, retiré, autre) et un devoir publié peut-il être modifié ou supprimé ? | Détermine les transitions, l’historique et les écrans. | Responsable métier / enseignant | À décider avant l’étape M4-1 |
| Les étudiants peuvent-ils seulement consulter les devoirs ou doivent-ils aussi les associer à leurs tâches personnelles ? | Détermine l’intégration entre M2 et M4. | Responsable métier | À décider avant l’intégration M3/M4 |
| Quel canal de rappel est retenu et quel comportement appliquer si l’envoi échoue ? | Détermine le traitement, les reprises et les critères de recette M3. | Responsable métier / responsable technique | Le courriel existe dans le code ; décision à confirmer |
| Un administrateur peut-il créer un compte, changer son rôle, désactiver son propre compte ou le dernier administrateur ? | Détermine les permissions et les garde-fous M5. | Responsable métier | À décider avant l’étape M5-1 |
| Faut-il conserver un journal des actions administratives et quelles données y enregistrer ? | Peut nécessiter une table et une durée de conservation. | Responsable métier / responsable technique | À décider avant toute journalisation dédiée |
| Le choix Bootstrap 5 de l’architecture est-il maintenu malgré l’interface Blade et les styles déjà présents ? | Évite de mélanger ou remplacer les conventions d’interface. | Responsable technique / enseignant | À confirmer avant une évolution visuelle importante |

## 9. Étapes d’implémentation

### Étape M1-1 — Réviser les comptes, l’inscription et l’authentification

- **Statut :** code et tests ciblés prêts pour le contrôle ; démonstration SMTP Gmail réel non validée.
- **Objectif :** permettre aux étudiants de s’inscrire et de se connecter eux-mêmes, avec une interface et des messages en français, sans possibilité d’auto-attribution d’un rôle privilégié.
- **Prérequis :** M1 existant ; choix confirmé d’une confirmation e-mail obligatoire avant l’accès aux pages privées.
- **Travaux :** conserver le rôle étudiant imposé côté serveur ; confirmer l’adresse e-mail ; bloquer l’accès privé aux comptes non vérifiés ou inactifs ; normaliser les adresses e-mail ; limiter les tentatives sensibles ; ne pas révéler l’existence d’un compte lors d’une demande de réinitialisation ; préparer Gmail SMTP par variables d’environnement ; franciser les interfaces, erreurs et courriels.
- **Emplacements indicatifs :** `app/Models/User.php`, `app/Http/Controllers/Auth/`, `app/Http/Requests/Auth/`, `routes/`, `config/app.php`, `config/mail.php`, `lang/fr/`, `lang/fr.json`, `resources/views/auth/`, `tests/Feature/Auth/`, `.env.example`, `README.md`.
- **Règles :** R01, R02, R11, R12, R14, R15.
- **Livrables :** inscription autonome étudiant, connexion, vérification/réinitialisation en français, configuration d’exemple Gmail sans secret et consignes de déploiement.
- **Tests :** rôle forcé étudiant malgré un rôle soumis par le client ; adresse normalisée ; e-mail de vérification émis ; accès privé refusé avant vérification ; connexion, déconnexion, compte inactif, erreur d’identifiants en français et demande de réinitialisation sans divulgation d’existence.
- **Critères de sortie pour le contrôle :** parcours applicatif et tests automatisés ciblés réussis ; la limite de réception Gmail est annoncée. L’envoi réel reste un prérequis distinct avant de promettre la délivrabilité SMTP en production.

### Étape 0 — Auditer l’existant et valider les décisions communes

- **Statut :** réalisée pour le périmètre M1/M2 du contrôle ; audit des modules M3 à M5 à poursuivre séparément.
- **Objectif :** transformer l’inventaire initial en état de référence vérifié.
- **Prérequis :** accès au dépôt et au document d’architecture.
- **Travaux :** examiner les routes, modèles, migrations, vues et tests de M1/M2 ; exécuter les tests concernés ; vérifier middleware d’utilisateur actif et rôles ; relever les écarts entre la conception et le dépôt. L’état de M3 à M5 reste à confirmer dans leurs étapes propres.
- **Emplacements indicatifs :** `routes/`, `app/`, `database/migrations/`, `resources/views/`, `tests/`.
- **Règles :** R01, R02, R14, R15.
- **Livrables :** inventaire M1/M2 actualisé dans ce plan, cahier des charges, architecture logicielle et guide de démonstration.
- **Tests :** suite complète exécutée ; noter exactement la commande et son résultat dans le bilan.
- **Critères de sortie :** état M1/M2 et limites vérifiés ; les fonctions absentes ou les vérifications manuelles restantes ne sont pas présentées comme acquises.

### Étape M2-1 — Valider le module de tâches pour le contrôle de cours

- **Statut :** création et consultation validées par les tests automatisés ; parcours manuel à effectuer par le groupe avant la séance.
- **Objectif :** démontrer un module métier fonctionnel répondant aux exigences du devoir : traitements réels, persistance, validation, contrôle d’accès, cas limite et erreurs.
- **Prérequis :** M1 disponible ; compte de démonstration préparé.
- **Travaux :** démontrer la liste, la création et la persistance des tâches ; confirmer le filtrage par propriétaire ; vérifier les données invalides, les champs facultatifs, l’échappement des contenus et le refus des accès non autorisés.
- **Emplacements indicatifs :** `app/Http/Controllers/TacheController.php`, `app/Models/Tache.php`, `routes/web.php`, `resources/views/taches/`, `tests/Feature/TacheTest.php`.
- **Règles :** R01 à R05, R14, R15.
- **Livrables :** création et consultation de tâches personnelles, tests automatisés et scénario de démonstration reproductible.
- **Tests :** création valide et invalide ; description et échéance facultatives ; propriétaire dérivé de la session ; isolation entre étudiants ; accès visiteur et rôle non étudiant refusé ; données XSS affichées comme texte.
- **Critères de sortie pour le contrôle :** les parcours de création et de consultation sont démontrés ; les tests ciblés réussissent ; les opérations de modification et de suppression, absentes du périmètre codé, ne sont pas annoncées comme réalisées.

### Étape M3-1 — Valider le calendrier et fiabiliser les rappels

- **Statut :** à faire — calendrier, rappel et commande repérés, validation opérationnelle restante.
- **Objectif :** garantir l’affichage cohérent des échéances personnelles et le traitement fiable des rappels.
- **Prérequis :** étape 0 validée ; étape M2-1 terminée pour les parcours touchés.
- **Travaux :** vérifier navigation entre mois, échéances proches et retards ; vérifier propriété des rappels, absence de doublon et annulation ; examiner le traitement en cas d’échec d’envoi pour éviter de marquer comme envoyé un rappel non transmis ; confirmer la configuration mail et le lancement régulier du Scheduler. Ne pas modifier la politique de reprise avant décision.
- **Emplacements indicatifs :** `app/Http/Controllers/CalendrierController.php`, `app/Console/Commands/EnvoyerRappels.php`, `app/Notifications/RappelTacheNotification.php`, `app/Models/Rappel.php`, `routes/console.php`, `resources/views/calendrier/`, `tests/Feature/CalendrierTest.php`.
- **Règles :** R01, R02, R05 à R07, R14, R15.
- **Livrables :** parcours calendrier validé, règles de traitement et de reprise documentées, tests ciblés.
- **Tests :** mois sans tâche, tâches terminées/en retard/à venir ; rappel d’une tâche propre ; tentative sur tâche d’un autre utilisateur ; date invalide ou après l’échéance ; tâche terminée ; exécution répétée ; échec simulé d’envoi ; accès par rôle.
- **Critères de sortie :** tous les cas convenus sont couverts ; aucune répétition ou fausse confirmation d’envoi n’est observée dans les tests ; configuration opérationnelle documentée sans prétendre qu’un courriel réel a été reçu si cela n’a pas été vérifié.

### Étape M4-1 — Cadrer le modèle et les règles des devoirs enseignants

- **Statut :** bloquée par décisions métier.
- **Objectif :** valider l’audience, le cycle de vie et les droits avant de créer une structure de données.
- **Prérequis :** étape 0 validée ; décisions sur le ciblage, les états et la modification/retrait.
- **Travaux :** choisir les destinataires avec les entités réellement disponibles ; définir les champs requis, statuts et transitions ; déterminer la visibilité d’un devoir retiré ou modifié et son effet sur le calendrier.
- **Emplacements indicatifs :** document de décision, puis `database/migrations/`, `app/Models/`, `app/Policies/` ou équivalent selon les conventions constatées.
- **Règles :** R01, R02, R08 à R10, R13, R14.
- **Livrables :** décisions approuvées, modèle de données et matrice des permissions.
- **Tests :** revue sur papier d’un enseignant autorisé, d’un enseignant non auteur, d’un étudiant ciblé et d’un étudiant non ciblé.
- **Critères de sortie :** aucun comportement d’audience ou de cycle de vie n’est laissé à une supposition d’implémentation.

### Étape M4-2 — Publier et consulter les devoirs

- **Statut :** bloquée jusqu’à validation de M4-1.
- **Objectif :** permettre la publication côté enseignant et la consultation limitée aux destinataires.
- **Prérequis :** M4-1 validée ; M1 disponible.
- **Travaux :** créer les migrations, modèle, validations, contrôles d’accès, routes et vues nécessaires ; définir explicitement les opérations de création, lecture, modification et retrait autorisées ; associer l’auteur depuis la session ; ne pas exposer les devoirs à une audience non autorisée.
- **Emplacements indicatifs :** `app/Http/Controllers/`, `app/Http/Requests/` si pertinent selon les conventions existantes, `app/Models/`, `app/Policies/`, `routes/web.php`, `resources/views/`, `tests/Feature/`.
- **Règles :** R01, R02, R08 à R10, R13 à R15.
- **Livrables :** parcours enseignant et étudiant, migrations réversibles, tests de permissions et documentation des règles.
- **Tests :** enseignant autorisé publie ; étudiant et compte inactif refusés en écriture ; enseignant tiers ne modifie ni ne retire un devoir interdit ; étudiant ciblé consulte ; étudiant hors cible ne reçoit pas le devoir ; validation des champs et dates ; retrait conforme à la décision.
- **Critères de sortie :** aucun accès non autorisé par URL directe ; audience respectée côté serveur ; migrations et tests ciblés réussis.

### Étape M3-2 — Intégrer les échéances des devoirs au calendrier

- **Statut :** bloquée jusqu’à M4-2.
- **Objectif :** rendre visibles les échéances des devoirs aux seuls utilisateurs autorisés.
- **Prérequis :** M3-1 et M4-2 validées ; règles d’audience confirmées.
- **Travaux :** intégrer la lecture des échéances dans le calendrier sans dupliquer les données d’un devoir ; déterminer si les devoirs génèrent des rappels et selon quelle règle, après décision métier.
- **Emplacements indicatifs :** contrôleur et vue du calendrier, tests du calendrier, modèle et requêtes M4.
- **Règles :** R02, R06, R09, R15.
- **Livrables :** calendrier intégré et tests d’accès couvrant tâches et devoirs.
- **Tests :** devoir visible à un destinataire ; devoir absent pour un non-destinataire ; échéance publiée/retirée ; absence de doublon avec une éventuelle tâche personnelle.
- **Critères de sortie :** les données restent filtrées côté serveur et les cas de calendrier convenus sont réussis.

### Étape M5-1 — Valider les permissions et garde-fous d’administration

- **Statut :** bloquée par décisions métier.
- **Objectif :** préciser les actions administratives et les protections contre une perte d’accès à l’administration.
- **Prérequis :** étape 0 validée ; décisions sur la création de compte, les rôles, la désactivation et la journalisation.
- **Travaux :** établir la matrice des actions ; préciser les règles de désactivation et de changement de rôle ; statuer sur le compte courant et le dernier administrateur ; déterminer si les actions sensibles doivent être journalisées.
- **Emplacements indicatifs :** décision métier, puis routes, policy et tests selon le code existant.
- **Règles :** R01, R02, R11 à R14.
- **Livrables :** matrice des permissions, règles de transition du statut et décision de journalisation.
- **Tests :** revue sur papier d’un utilisateur non administrateur, d’un administrateur, du compte cible et du dernier administrateur.
- **Critères de sortie :** les opérations sensibles et cas de verrouillage administratif sont explicitement décidés.

### Étape M5-2 — Réaliser la gestion administrative des comptes

- **Statut :** bloquée jusqu’à M5-1.
- **Objectif :** fournir les fonctions d’administration approuvées sans exposer de secrets.
- **Prérequis :** M5-1 validée ; M1 disponible.
- **Travaux :** créer la liste et la recherche de comptes ainsi que les actions approuvées ; appliquer les autorisations au serveur ; empêcher la modification des champs non autorisés ; appliquer les garde-fous sur la désactivation et les rôles ; ajouter un journal uniquement si la décision le demande.
- **Emplacements indicatifs :** contrôleur, policy, routes, vues Blade et tests `Feature` d’administration selon les conventions existantes.
- **Règles :** R01, R02, R11 à R15.
- **Livrables :** écran d’administration, règles contrôlées côté serveur, tests et éventuelles migrations de journalisation.
- **Tests :** accès interdit aux étudiants et enseignants ; compte inactif refusé ; recherche et liste sans mot de passe ni secret ; changement de statut autorisé/refusé ; protections du compte courant et du dernier administrateur selon décision ; journalisation si requise.
- **Critères de sortie :** seuls les droits approuvés sont applicables, aucun secret n’est rendu visible et les tests ciblés passent.

### Étape 9 — Recette intégrée et validation de livraison

- **Statut :** à faire.
- **Objectif :** vérifier les parcours complets sans confondre tests automatisés et validation métier.
- **Prérequis :** M2, M3, M4 et M5 terminés ; questions bloquantes tranchées.
- **Travaux :** exécuter les tests concernés, contrôler migrations et retours arrière, parcourir l’application selon chaque rôle, vérifier calendrier/rappels et consigner les limites.
- **Scénarios :** étudiant gère ses tâches et échéances ; calendrier affiche seulement les données autorisées ; enseignant publie un devoir pour l’audience validée ; administrateur gère un compte selon la matrice ; tentatives d’accès croisé refusées.
- **Livrables :** bilan daté avec fichiers réellement modifiés, commandes exécutées, résultats, défauts restants et validation du responsable.
- **Critères de sortie :** critères d’acceptation approuvés satisfaits ; aucun défaut bloquant ; toute limite ou vérification non exécutée est explicitement signalée.
- **Déploiement :** action séparée nécessitant une autorisation explicite.

## 10. Bilan d’exécution à compléter par étape

- Statut : à faire / en cours / bloquée / à valider / validée.
- Fichiers modifiés : chemins réellement modifiés.
- Tests : commandes et résultats réels, avec date.
- Preuves : résultats de tests ou parcours observés.
- Limites : tests non exécutés, dépendances manquantes, décisions en attente.
- Validation : nom du validateur, date et décision de passage à l’étape suivante.

### Bilan M1/M2 — contrôle de cours, 29 octobre 2026

- **Statut :** code et tests automatisés prêts ; recette et validation métier devant le groupe à réaliser.
- **Modules :** M1 — comptes/authentification ; M2 — création et consultation des tâches.
- **Documentation :** `docs/cahier-des-charges.md`, `docs/architecture-logicielle.md`, `docs/guide-demonstration-modules.md`.
- **Commande ciblée :** `php artisan test --compact tests/Feature/Auth tests/Feature/TacheTest.php` — 38 tests réussis, 124 assertions.
- **Commande complète :** `php artisan test --compact` — 55 tests réussis, 192 assertions.
- **Autres vérifications :** Pint réussi ; `npm run build` réussi ; focus clavier et absence de débordement horizontal constatés sur les pages de connexion et d’inscription à 390 px.
- **Limites :** parcours manuel de tâches, vérification WCAG complète et validation Gmail SMTP réelle restent à faire ; Gmail a déjà refusé une authentification avec `535-5.7.8`. Modification/suppression des tâches non implémentées et non requises explicitement par le devoir.
- **Validation :** à compléter par le groupe et le professeur lors de la séance.

Ne pas cocher une étape sur la seule base de la présence de fichiers ou d’un compte rendu de l’IA.
