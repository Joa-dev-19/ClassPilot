# ClassPilot

ClassPilot est une application web qui aide les élèves et les étudiants à organiser leurs tâches scolaires, leurs échéances et leurs rappels. L’application est développée avec Laravel, Blade, Vite et Tailwind CSS.

## Fonctionnalités

- **Comptes et accès :** inscription étudiante, connexion, vérification de l’adresse e-mail et gestion du profil.
- **Tâches personnelles :** création, consultation, modification et suppression des tâches.
- **Calendrier et rappels :** calendrier des échéances et rappels par courriel programmés par l’étudiant.
- **Devoirs enseignants :** création en brouillon, publication pour tous les étudiants, une filière ou une classe, modification et archivage. Les échéances visibles apparaissent dans le calendrier étudiant.
- **Administration :** recherche et consultation des comptes, activation/désactivation, gestion des filières et classes, et rattachement des étudiants.

Les devoirs ne gèrent pas encore les groupes, le dépôt de travaux ou les notes.

## Prérequis

- PHP 8.3 ou une version compatible avec `composer.json` (PHP 8.4 est utilisé dans l’environnement de développement).
- Composer.
- Node.js et npm.
- Une base de données configurée dans `.env` (SQLite pour le développement par défaut ; utiliser une base persistante pour la production).

## Installation en local

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Par défaut, l’environnement local écrit les e-mails dans `storage/logs/laravel.log`. Cela permet de vérifier les liens de confirmation et de réinitialisation sans transmettre de message réel.

## Inscription et accès

L’inscription publique crée uniquement un compte étudiant actif. Le rôle envoyé depuis le navigateur n’est jamais utilisé pour attribuer les autorisations. Après inscription, l’étudiant doit confirmer son adresse e-mail avant d’accéder aux pages privées.

Pour les comptes enseignant et administrateur, utiliser une procédure de création contrôlée ; ne pas rendre le choix du rôle disponible sur le formulaire public.

## Configurer l’envoi de courriels avec Gmail

ClassPilot peut envoyer les confirmations d’adresse, les liens de réinitialisation et les rappels avec Gmail SMTP.

1. Active la validation en deux étapes sur le compte Google qui servira d’expéditeur.
2. Crée un **mot de passe d’application** dans les paramètres de sécurité Google. Le mot de passe normal du compte Google ne doit pas être utilisé.
3. Configure les variables suivantes dans `.env` en local ou dans le gestionnaire de secrets de l’hébergeur en production :

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adresse-expediteur@gmail.com
MAIL_PASSWORD=mot-de-passe-application-google
MAIL_FROM_ADDRESS=adresse-expediteur@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

`MAIL_FROM_ADDRESS` doit correspondre au compte Gmail authentifié. Ne publie pas le mot de passe d’application, ne le place pas dans le dépôt et ne le transmets pas dans une conversation. Si un secret a été exposé, révoque-le depuis le compte Google et génère-en un nouveau.

Après modification de la configuration :

```sh
php artisan config:clear
php artisan config:cache
```

Vérifie l’envoi avec une inscription de test et confirme que le message arrive réellement. La configuration seule ne prouve pas la délivrabilité. Gmail applique des limites d’envoi ; pour un volume de production important, privilégier un fournisseur transactionnel.

## Tests et compilation

```sh
php artisan test
npm run build
```

Les notifications doivent être simulées dans les tests automatisés. Un essai SMTP réel doit être effectué séparément dans un environnement contrôlé avec des identifiants non partagés.

## Préparer un déploiement

Les étapes ci-dessous sont une checklist générique. Le fournisseur d’hébergement, les valeurs de production et le domaine doivent être configurés séparément.

1. Configurer `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` avec l’URL HTTPS, `APP_LOCALE=fr` et `APP_FALLBACK_LOCALE=fr`.
2. Fournir une clé `APP_KEY` persistante et propre à l’environnement ; ne pas en générer une nouvelle à chaque livraison.
3. Configurer les identifiants de la base de données de production par le gestionnaire de secrets de l’hébergeur. Ne pas utiliser une base de test ou un fichier SQLite éphémère pour les données de production.
4. Configurer les variables Gmail SMTP décrites ci-dessus dans le gestionnaire de secrets.
5. Installer les dépendances de production et compiler les ressources :

   ```sh
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

6. Exécuter les migrations de manière contrôlée :

   ```sh
   php artisan migrate --force
   ```

7. Mettre en place une sauvegarde de base de données et vérifier la procédure de restauration avant la première livraison.
8. Configurer le planificateur Laravel. En production, ajouter une seule entrée cron par minute :

   ```cron
   * * * * * cd /chemin/vers/classpilot && php artisan schedule:run >> /dev/null 2>&1
   ```

   En développement local, utiliser `php artisan schedule:work` dans un terminal dédié.