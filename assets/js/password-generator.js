document.addEventListener('DOMContentLoaded', function() {
    const lengthSlider = document.getElementById('length');
    const lengthValue = document.getElementById('length-value');
    const generatedPassword = document.getElementById('generated_password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthFeedback = document.getElementById('strength-feedback');
    
    // Update length value display
    lengthSlider.addEventListener('input', function() {
        lengthValue.textContent = this.value;
        updatePasswordStrength();
    });
    
    // Generate password on form submit
    document.querySelector('.generator-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updatePasswordStrength();
    });
    
    // Copy password button
    document.querySelector('.copy-password').addEventListener('click', function() {
        if (!generatedPassword.value) return;
        
        generatedPassword.select();
        document.execCommand('copy');
        
        // Show tooltip
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i> Copied!';
        
        setTimeout(() => {
            this.innerHTML = originalText;
        }, 2000);
    });
    
    // Update password strength meter
    function updatePasswordStrength() {
        const length = parseInt(lengthSlider.value);
        const uppercase = document.getElementById('uppercase').checked;
        const lowercase = document.getElementById('lowercase').checked;
        const numbers = document.getElementById('numbers').checked;
        const symbols = document.getElementById('symbols').checked;
        
        // Calculate strength (0-100)
        let strength = 0;
        let feedback = '';
        
        // Length contributes up to 50 points
        strength += Math.min(length / 64 * 50, 50);
        
        // Character variety contributes up to 50 points
        const varietyCount = [uppercase, lowercase, numbers, symbols].filter(Boolean).length;
        strength += (varietyCount / 4) * 50;
        
        // Adjust feedback based on strength
        if (strength < 30) {
            feedback = 'Very Weak';
            strengthBar.style.backgroundColor = '#f72585';
        } else if (strength < 50) {
            feedback = 'Weak';
            strengthBar.style.backgroundColor = '#f8961e';
        } else if (strength < 70) {
            feedback = 'Moderate';
            strengthBar.style.backgroundColor = '#4cc9f0';
        } else if (strength < 90) {
            feedback = 'Strong';
            strengthBar.style.backgroundColor = '#4361ee';
        } else {
            feedback = 'Very Strong';
            strengthBar.style.backgroundColor = '#3a0ca3';
        }
        
        strengthBar.style.width = `${Math.min(strength, 100)}%`;
        strengthFeedback.textContent = feedback;
    }
    
    // Initialize strength meter
    updatePasswordStrength();
});