<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$generated_password = '';
$length = 16;
$options = [
    'uppercase' => true,
    'lowercase' => true,
    'numbers' => true,
    'symbols' => true
];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $length = (int)$_POST['length'];
    $options = [
        'uppercase' => isset($_POST['uppercase']),
        'lowercase' => isset($_POST['lowercase']),
        'numbers' => isset($_POST['numbers']),
        'symbols' => isset($_POST['symbols'])
    ];
    
    try {
        $generated_password = generate_password($length, $options);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="content-header">
    <h1>Générateur de mots de passe</h1>
    <p>Créez des mots de passe forts et aléatoires pour vos comptes</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="generator-container">
    <form method="POST" class="generator-form">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        <div class="form-group">
            <label for="generated_password">Mot de passe généré</label>
            <div class="password-display">
                <input type="text" id="generated_password" name="generated_password" value="<?= htmlspecialchars($generated_password) ?>" readonly>
                <button type="button" class="btn btn-sm copy-password" data-target="generated_password">
                    <i class="fas fa-copy"></i> Copier
                </button>
            </div>
        </div>
        
        <div class="form-group">
            <label for="length">Longueur du mot de passe : <span id="length-value"><?= $length ?></span></label>
            <input type="range" id="length" name="length" min="8" max="64" value="<?= $length ?>">
        </div>
        
        <div class="form-group options-group">
            <label>Types de caractères :</label>
            <div class="options-grid">
                <div class="option-item">
                    <input type="checkbox" id="uppercase" name="uppercase" <?= $options['uppercase'] ? 'checked' : '' ?>>
                    <label for="uppercase">Majuscules (A-Z)</label>
                </div>
                <div class="option-item">
                    <input type="checkbox" id="lowercase" name="lowercase" <?= $options['lowercase'] ? 'checked' : '' ?>>
                    <label for="lowercase">Minuscules (a-z)</label>
                </div>
                <div class="option-item">
                    <input type="checkbox" id="numbers" name="numbers" <?= $options['numbers'] ? 'checked' : '' ?>>
                    <label for="numbers">Chiffres (0-9)</label>
                </div>
                <div class="option-item">
                    <input type="checkbox" id="symbols" name="symbols" <?= $options['symbols'] ? 'checked' : '' ?>>
                    <label for="symbols">Symboles (!@#$%^&*)</label>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-random"></i> Générer un mot de passe
            </button>
        </div>
    </form>
    
    <div class="password-strength">
        <h3>Force du mot de passe</h3>
        <div class="strength-meter">
            <div class="strength-bar" id="strength-bar"></div>
        </div>
        <div class="strength-feedback" id="strength-feedback"></div>
        
        <h3>Conseils pour les mots de passe</h3>
        <ul class="tips-list">
            <li>Utilisez au moins 12 caractères</li>
            <li>Incluez différents types de caractères</li>
            <li>Évitez les mots courants ou les séquences</li>
            <li>Ne réutilisez pas les mots de passe</li>
            <li>Envisagez une phrase secrète pour plus de facilité</li>
        </ul>
    </div>
</div>

<script src="/assets/js/password-generator.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>