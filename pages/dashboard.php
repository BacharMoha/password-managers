<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

$user = get_current_user_data();
$password_count = get_user_passwords_count($user['id']);
?>

<div class="content-header">
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <?= htmlspecialchars($user['username']) ?></p>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-key"></i>
            <h3>Mots de passe enregistrés</h3>
        </div>
        <div class="card-body">
            <h2><?= $password_count ?></h2>
            <p>Total des mots de passe stockés</p>
        </div>
        <div class="card-footer">
            <a href="/pages/view-passwords.php" class="btn btn-sm">Voir tout</a>
        </div>
    </div>
    
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-plus-circle"></i>
            <h3>Ajouter un mot de passe</h3>
        </div>
        <div class="card-body">
            <p>Enregistrer un nouveau mot de passe</p>
        </div>
        <div class="card-footer">
            <a href="/pages/add-password.php" class="btn btn-sm">Ajouter</a>
        </div>
    </div>
    
    <div class="dashboard-card">
        <div class="card-header">
            <i class="fas fa-random"></i>
            <h3>Générateur de mots de passe</h3>
        </div>
        <div class="card-body">
            <p>Créer des mots de passe sécurisés</p>
        </div>
        <div class="card-footer">
            <a href="/pages/generator.php" class="btn btn-sm">Générer</a>
        </div>
    </div>
</div>

<div class="recent-passwords">
    <h2>Mots de passe récemment ajoutés</h2>
    <?php
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, site_name, site_url, username, created_at 
                         FROM passwords 
                         WHERE user_id = ? 
                         ORDER BY created_at DESC 
                         LIMIT 5");
    $stmt->execute([$user['id']]);
    $recent_passwords = $stmt->fetchAll();
    
    if (empty($recent_passwords)): ?>
        <p>Aucun mot de passe enregistré. <a href="/pages/add-password.php">Ajoutez votre premier mot de passe</a></p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom du site</th>
                    <th>URL</th>
                    <th>Identifiant</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_passwords as $password): ?>
                    <tr>
                        <td><?= htmlspecialchars($password['site_name']) ?></td>
                        <td>
                            <?php if ($password['site_url']): ?>
                                <a href="<?= htmlspecialchars($password['site_url']) ?>" target="_blank" rel="noopener noreferrer">
                                    <?= parse_url($password['site_url'], PHP_URL_HOST) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($password['username']) ?></td>
                        <td><?= date('j M Y', strtotime($password['created_at'])) ?></td>
                        <td>
                            <a href="/pages/view-passwords.php?view=<?= $password['id'] ?>" class="btn btn-sm">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>