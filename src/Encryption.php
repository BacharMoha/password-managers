<?php
class Encryption {
    private $key;
    
    public function __construct($key) {
        // Accepte clé en base64 ou format brut
        if (strlen($key) === 44) {
            $key = base64_decode($key);
            if ($key === false) {
                throw new InvalidArgumentException("Clé base64 invalide");
            }
        }
        
        if (strlen($key) !== 32) {
            throw new InvalidArgumentException(
                "La clé doit faire 32 octets. Reçu: ".strlen($key)." octets"
            );
        }
        
        $this->key = $key;
    }
    
    public function encrypt($plaintext) {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt(
            $plaintext,
            ENCRYPTION_METHOD,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );
        return base64_encode($iv.$ciphertext);
    }
    
    public function decrypt($ciphertext) {
        $data = base64_decode($ciphertext);
        $iv = substr($data, 0, ENCRYPTION_IV_LENGTH);
        $ciphertext = substr($data, ENCRYPTION_IV_LENGTH);
        return openssl_decrypt(
            $ciphertext,
            ENCRYPTION_METHOD,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
    
    public static function generateKey() {
        try {
            return random_bytes(32);
        } catch (Exception $e) {
            throw new RuntimeException("Échec génération clé: ".$e->getMessage());
        }
    }
}
?>