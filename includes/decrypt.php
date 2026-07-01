<?php

function decrypt_file_contents(string $encryptedPath, string $ivBase64): string
{
    $key = get_encryption_key();
    $iv = base64_decode($ivBase64, true);

    if ($iv === false) {
        throw new RuntimeException('Invalid IV.');
    }

    $ciphertext = file_get_contents($encryptedPath);
    if ($ciphertext === false) {
        throw new RuntimeException('Unable to read encrypted file.');
    }

    $plaintext = openssl_decrypt($ciphertext, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    if ($plaintext === false) {
        throw new RuntimeException('Decryption failed. The file may be corrupted or the key is incorrect.');
    }

    return $plaintext;
}

function stream_decrypted_file(string $encryptedPath, string $ivBase64, string $downloadName, string $mimeType): void
{
    $plaintext = decrypt_file_contents($encryptedPath, $ivBase64);

    header('Content-Description: File Transfer');
    header('Content-Type: ' . ($mimeType !== '' ? $mimeType : 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"');
    header('Content-Length: ' . strlen($plaintext));
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');

    echo $plaintext;
}
