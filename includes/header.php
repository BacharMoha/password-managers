<?php if (is_logged_in()): ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $page_title ?? 'Tableau de bord' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="/assets/img/logo.svg" alt="Logo" class="logo">
                <h1><?= APP_NAME ?></h1>
            </div>
            <ul class="sidebar-menu">
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="/pages/dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'add-password.php' ? 'active' : '' ?>">
                    <a href="/pages/add-password.php"><i class="fas fa-plus-circle"></i> Ajouter un mot de passe</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'view-passwords.php' ? 'active' : '' ?>">
                    <a href="/pages/view-passwords.php"><i class="fas fa-key"></i> Voir les mots de passe</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'generator.php' ? 'active' : '' ?>">
                    <a href="/pages/generator.php"><i class="fas fa-random"></i> Générateur de mots de passe</a>
                </li>
                <li>
                    <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <p>Version <?= APP_VERSION ?></p>
                <p>&copy; <?= date('Y') ?> SecurePass</p>
            </div>
        </nav>
        <main class="main-content">
<?php endif; ?>