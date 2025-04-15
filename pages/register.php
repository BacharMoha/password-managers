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
    $confirm_password = $_POST['confirm_password'];
    $csrf_token = $_POST['csrf_token'];
    
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Jeton CSRF invalide';
    } elseif (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < 12) {
        $error = 'Le mot de passe doit contenir au moins 12 caractères';
    } else {
        $db = Database::getInstance();
        
        // Vérifie si le nom d'utilisateur existe déjà
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = 'Ce nom d\'utilisateur existe déjà';
        } else {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $error = 'Ce nom d\'utilisateur existe déjà';
            } else {
                try {
                    $encryption_key = Encryption::generateKey();
                    $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    $db->beginTransaction();
                    $stmt = $db->prepare("INSERT INTO users (username, master_password_hash, encryption_key) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $password_hash, base64_encode($encryption_key)]);
                    $db->commit();
                    
                    // Redirection vers login avec message de succès
                    redirect('/pages/login.php?registered=1');
                    
                } catch (Exception $e) {
                    if ($db->inTransaction()) $db->rollBack();
                    $error = 'Échec de l\'inscription: ' . $e->getMessage();
                    error_log("Erreur inscription: ".$e->getMessage());
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Inscription</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="/assets/img/logo.svg" alt="Logo" class="auth-logo">
                <h1>Créez votre compte</h1>
                <p>Votre mot de passe maître chiffrera tous vos mots de passe stockés</p>
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
                    <small class="form-text">Minimum 12 caractères</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmez le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Déjà un compte ? <a href="/pages/login.php">Connectez-vous ici</a></p>
            </div>
        </div>
    </div>
</body>
</html>