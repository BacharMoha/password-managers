<?php
require_once __DIR__ . '/../includes/config.php';

if (is_logged_in()) {
    redirect('/pages/dashboard.php');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'];
    
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Jeton CSRF invalide';
    } elseif (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, master_password_hash, encryption_key FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['master_password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['encryption_key'] = base64_decode($user['encryption_key']);
            
            // Régénère l'ID de session pour prévenir les attaques de fixation
            session_regenerate_id(true);
            
            redirect('/pages/dashboard.php');
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Connexion</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="/assets/img/logo.svg" alt="Logo" class="auth-logo">
                <h1>Bienvenue sur <?= APP_NAME ?></h1>
                <p>Connectez-vous pour accéder à vos mots de passe</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe maître</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Pas de compte ? <a href="/pages/register.php">Inscrivez-vous ici</a></p>
            </div>
        </div>
    </div>
</body>
</html>