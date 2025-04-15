<?php
require_once __DIR__ . '/config.php';

// Redirect to login if not authenticated
if (!is_logged_in() && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    redirect('/pages/login.php');
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('/pages/login.php');
}

// Get current user info
function get_current_user_data() {
    if (!is_logged_in()) return null;
    
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, username, created_at, encryption_key FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) return null;

    if (!isset($_SESSION['encryption_key'])) {
        try {
            $raw_key = base64_decode($user['encryption_key']);
            if (strlen($raw_key) !== 32) {
                error_log("Clé invalide pour user ".$user['id']." - Régénération...");
                $new_key = random_bytes(32);
                $db->prepare("UPDATE users SET encryption_key = ? WHERE id = ?")
                   ->execute([base64_encode($new_key), $user['id']]);
                $raw_key = $new_key;
            }
            $_SESSION['encryption_key'] = $raw_key;
        } catch (Exception $e) {
            error_log("Erreur clé chiffrement: ".$e->getMessage());
            session_destroy();
            redirect('/pages/login.php');
        }
    }
    
    return $user;
}
?>