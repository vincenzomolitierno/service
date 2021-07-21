<?php

//$key should have been previously generated in a cryptographically safe way, like openssl_random_pseudo_bytes
$plaintext = "C291C4EAFC0571E90AB6D31C2FE37E16296405C188C38";
echo $plaintext."<br>";
$cipher = "aes-128-gcm";
if (in_array($cipher, openssl_get_cipher_methods()))
{
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
    echo $cipher . '<br>';
    echo base64_encode($iv) . '<br>';
    echo base64_encode($tag) . '<br>';
    echo $ciphertext."<br>";
    //store $cipher, $iv, and $tag for decryption later
    $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
    echo $original_plaintext."<br>";
}
?>