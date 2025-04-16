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
    function calculate_password_strength($password) {
        $score = 0;
        
        // Longueur du mot de passe
        $length = strlen($password);
        if ($length < 8) return 0;
        
        $score += min(($length - 8) * 2, 20); // Max 20 points pour la longueur
        
        // Diversité des caractères
        $types = 0;
        if (preg_match('/[A-Z]/', $password)) $types++;
        if (preg_match('/[a-z]/', $password)) $types++;
        if (preg_match('/[0-9]/', $password)) $types++;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $types++;
        
        $score += ($types - 1) * 10; // 0-30 points pour la diversité
        
        // Vérification des motifs simples
        if (preg_match('/(.)\1{2,}/', $password)) $score -= 15;
        if (preg_match('/123|abc|qwe|asd|zxc/', strtolower($password))) $score -= 20;
        
        return max(0, min(100, $score)); // Score entre 0 et 100
    }
    return $user;
}
?>