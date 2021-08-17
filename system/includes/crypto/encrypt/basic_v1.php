<?php 

function encryptString($plaintext, $password, $encoding = null) {
    $iv = openssl_random_pseudo_bytes(16);
    $ciphertext = openssl_encrypt($plaintext, "AES-256-CBC", hash('sha256', $password, true), OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext.$iv, hash('sha256', $password, true), true);
    return $encoding == "hex" ? bin2hex($iv.$hmac.$ciphertext) : ($encoding == "base64" ? base64_encode($iv.$hmac.$ciphertext) : $iv.$hmac.$ciphertext);
}

?>