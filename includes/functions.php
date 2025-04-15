<?php
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generate_password($length = 16, $options = []) {
    $defaults = [
        'uppercase' => true,
        'lowercase' => true,
        'numbers' => true,
        'symbols' => true
    ];
    
    $options = array_merge($defaults, $options);
    
    $chars = '';
    if ($options['uppercase']) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($options['lowercase']) $chars .= 'abcdefghijklmnopqrstuvwxyz';
    if ($options['numbers']) $chars .= '0123456789';
    if ($options['symbols']) $chars .= '!@#$%^&*()-_=+[]{}|;:,.<>?';
    
    if (empty($chars)) {
        throw new Exception('At least one character type must be selected');
    }
    
    $password = '';
    $charLength = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $charLength - 1)];
    }
    
    return $password;
}

function get_user_passwords_count($user_id) {
    // Ajoutez une vérification du type
    if (!is_numeric($user_id)) {
        return 0;
    }
    
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM passwords WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}
?>