<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$user = get_current_user_data();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitize_input($_POST['site_name']);
    $site_url = sanitize_input($_POST['site_url']);
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $notes = sanitize_input($_POST['notes']);
    $csrf_token = $_POST['csrf_token'];
    
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Jeton CSRF invalide';
    } elseif (empty($site_name) || empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires';
    } else {
        try {
            if (!isset($_SESSION['encryption_key']) || strlen($_SESSION['encryption_key']) !== 32) {
                throw new Exception("Clé de chiffrement invalide. Veuillez vous reconnecter.");
            }
            
            $encryption = new Encryption($_SESSION['encryption_key']);
            $encrypted_password = $encryption->encrypt($password);
            
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO passwords 
                                (user_id, site_name, site_url, username, encrypted_password, notes) 
                                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user['id'],
                $site_name,
                $site_url,
                $username,
                $encrypted_password,
                $notes
            ]);
            
            $success = 'Mot de passe enregistré avec succès !';
            
            // Réinitialiser les champs
            $site_name = $site_url = $username = $notes = '';
        } catch (Exception $e) {
            $error = 'Échec de l\'enregistrement : ' . $e->getMessage();
            error_log("Erreur add-password: ".$e->getMessage());
        }
    }
}

?>

<div class="content-header">
    <h1>Ajouter un nouveau mot de passe</h1>
    <p>Enregistrez un nouveau mot de passe dans votre coffre-fort sécurisé</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" class="password-form">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
    
    <div class="form-group">
        <label for="site_name">Nom du site/application *</label>
        <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($site_name ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="site_url">URL du site</label>
        <input type="url" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label for="username">Nom d'utilisateur/Email *</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="password">Mot de passe *</label>
        <div class="password-input-group">
            <input type="password" id="password" name="password" required>
            <button type="button" class="btn btn-sm toggle-password" data-target="password">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-sm generate-password" data-target="password">
                <i class="fas fa-random"></i> Générer
            </button>
        </div>
    </div>
    
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($notes ?? '') ?></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <a href="/pages/dashboard.php" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<script src="/assets/js/main.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>