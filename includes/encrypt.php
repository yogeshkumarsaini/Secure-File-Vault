<?php

function get_encryption_key(): string
{
    $key = hex2bin(APP_ENCRYPTION_KEY_HEX);
    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('Invalid APP_ENCRYPTION_KEY_HEX: must be 64 hex chars (32 bytes).');
    }
    return $key;
}

function encrypt_file(string $sourcePath, string $destPath): string
{
    $key = get_encryption_key();
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = random_bytes($ivLength);

    $plaintext = file_get_contents($sourcePath);
    if ($plaintext === false) {
        throw new RuntimeException('Unable to read source file for encryption.');
    }

    $ciphertext = openssl_encrypt($plaintext, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) {
        throw new RuntimeException('Encryption failed.');
    }

    if (file_put_contents($destPath, $ciphertext) === false) {
        throw new RuntimeException('Unable to write encrypted file.');
    }

    unset($plaintext);

    return base64_encode($iv);
}

function file_checksum(string $path): string
{
    return hash_file('sha256', $path);
}
