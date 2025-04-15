<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$user = get_current_user_data();

// Vérifiez que $user est valide avant de l'utiliser
if (!$user || !isset($user['id'])) {
    die("Erreur: Impossible de récupérer les données utilisateur");
}

$password_count = get_user_passwords_count($user['id']);

$error = '';
$search = $_GET['search'] ?? '';
$view_id = $_GET['view'] ?? null;

// Handle password deletion
if (isset($_POST['delete_id'])) {
    $password_id = $_POST['delete_id'];
    $csrf_token = $_POST['csrf_token'];
    
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Invalid CSRF token';
    } else {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
        $stmt->execute([$password_id, $user['id']]);
        
        if ($stmt->rowCount() > 0) {
            $success = 'Password successfully deleted!';
        } else {
            $error = 'Password not found or you do not have permission to delete it';
        }
    }
}

// Get all passwords for the user
$db = Database::getInstance();
$query = "SELECT id, site_name, site_url, username, created_at, updated_at 
          FROM passwords 
          WHERE user_id = ?";
$params = [$user['id']];

if ($search) {
    $query .= " AND (site_name LIKE ? OR username LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY site_name ASC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$passwords = $stmt->fetchAll();

// Get specific password if viewing details
$password_details = null;
if ($view_id) {
    $stmt = $db->prepare("SELECT * FROM passwords WHERE id = ? AND user_id = ?");
    $stmt->execute([$view_id, $user['id']]);
    $password_details = $stmt->fetch();
    
    if ($password_details) {
        try {
            $encryption = new Encryption($_SESSION['encryption_key']);
            $decrypted_password = $encryption->decrypt($password_details['encrypted_password']);
            $password_details['password'] = $decrypted_password;
        } catch (Exception $e) {
            $error = 'Failed to decrypt password: ' . $e->getMessage();
        }
    }
}
?>

<div class="content-header">
    <h1>Your Passwords</h1>
    <p>Manage your stored passwords securely</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="password-actions">
    <form method="GET" class="search-form">
        <div class="form-group">
            <input type="text" name="search" placeholder="Search by site or username" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-sm">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if ($search): ?>
                <a href="/pages/view-passwords.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
    
    <a href="/pages/add-password.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

<div class="password-container">
    <?php if ($password_details): ?>
        <div class="password-details">
            <div class="details-header">
                <h2><?= htmlspecialchars($password_details['site_name']) ?></h2>
                <a href="/pages/view-passwords.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to list
                </a>
            </div>
            
            <div class="details-body">
                <div class="detail-row">
                    <label>Site URL:</label>
                    <p>
                        <?php if ($password_details['site_url']): ?>
                            <a href="<?= htmlspecialchars($password_details['site_url']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($password_details['site_url']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not specified</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="detail-row">
                    <label>Username:</label>
                    <p><?= htmlspecialchars($password_details['username']) ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Password:</label>
                    <div class="password-display">
                        <input type="password" value="<?= htmlspecialchars($password_details['password']) ?>" readonly id="password-display">
                        <button type="button" class="btn btn-sm toggle-password" data-target="password-display">
                            <i class="fas fa-eye"></i> Show
                        </button>
                        <button type="button" class="btn btn-sm copy-password" data-target="password-display">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                
                <?php if ($password_details['notes']): ?>
                    <div class="detail-row">
                        <label>Notes:</label>
                        <p><?= nl2br(htmlspecialchars($password_details['notes'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <label>Created:</label>
                    <p><?= date('M j, Y g:i A', strtotime($password_details['created_at'])) ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Last Updated:</label>
                    <p><?= date('M j, Y g:i A', strtotime($password_details['updated_at'])) ?></p>
                </div>
            </div>
            
            <div class="details-footer">
                <form method="POST" class="delete-form">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="delete_id" value="<?= $password_details['id'] ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('etes vous sur de vouloir supprimer?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <?php if (empty($passwords)): ?>
            <div class="empty-state">
                <i class="fas fa-key"></i>
                <h3>No passwords stored yet</h3>
                <p>Get started by adding your first password</p>
                <a href="/pages/add-password.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Password
                </a>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Site Name</th>
                        <th>Username</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($passwords as $password): ?>
                        <tr>
                            <td><?= htmlspecialchars($password['site_name']) ?></td>
                            <td><?= htmlspecialchars($password['username']) ?></td>
                            <td><?= date('M j, Y', strtotime($password['created_at'])) ?></td>
                            <td>
                                <a href="/pages/view-passwords.php?view=<?= $password['id'] ?>" class="btn btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="/assets/js/password-view.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>