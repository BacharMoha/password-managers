document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour de l'affichage de la longueur
    const lengthSlider = document.getElementById('length');
    const lengthValue = document.getElementById('length-value');
    
    if (lengthSlider && lengthValue) {
        lengthValue.textContent = lengthSlider.value;
        lengthSlider.addEventListener('input', function() {
            lengthValue.textContent = this.value;
        });
    }

    // Gestion de la copie du mot de passe
    document.querySelectorAll('.copy-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input) {
                input.select();
                document.execCommand('copy');
                
                // Feedback visuel
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copié!';
                this.classList.add('success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('success');
                }, 2000);
            }
        });
    });

    // Calcul de la force du mot de passe
    const passwordInput = document.getElementById('generated_password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthFeedback = document.getElementById('strength-feedback');
    
    function updatePasswordStrength() {
        const password = passwordInput.value;
        
        if (!password) {
            strengthBar.style.width = '0%';
            strengthFeedback.textContent = '';
            return;
        }
        
        let strength = 0;
        const length = password.length;
        
        // Points pour la longueur
        strength += Math.min(length * 3, 40);
        
        // Points pour la diversité
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSymbol = /[^A-Za-z0-9]/.test(password);
        
        const diversity = [hasUpper, hasLower, hasNumber, hasSymbol].filter(Boolean).length;
        strength += (diversity - 1) * 15;
        
        // Ajustement final
        strength = Math.max(0, Math.min(100, strength));
        
        // Mise à jour de l'UI
        strengthBar.style.width = strength + '%';
        
        // Couleur et feedback
        if (strength < 30) {
            strengthBar.style.backgroundColor = '#ff4d4d';
            strengthFeedback.textContent = 'Faible';
        } else if (strength < 70) {
            strengthBar.style.backgroundColor = '#ffcc00';
            strengthFeedback.textContent = 'Moyen';
        } else {
            strengthBar.style.backgroundColor = '#4CAF50';
            strengthFeedback.textContent = 'Fort';
        }
    }
    
    // Écouteur d'événement
    if (passwordInput) {
        passwordInput.addEventListener('input', updatePasswordStrength);
        
        // Mettre à jour au chargement si mot de passe déjà généré
        if (passwordInput.value) {
            updatePasswordStrength();
        }
    }
});