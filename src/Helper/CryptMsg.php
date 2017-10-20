<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.10.17
 * Time: 12:17
 */

namespace CloudTool\Helper;


class CryptMsg
{

    public function ssl_encrypt ($plainData, $publicKeyFile) {
        if ( ! file_exists($publicKeyFile))
            throw new \Exception("Public Key File $publicKeyFile not found");
        $publicKey = openssl_get_publickey(file_get_contents($publicKeyFile));
        if ( ! $publicKey)
            throw new \Exception("Cannot load PublicKey: " . openssl_error_string());

        $sealedData = $ekeys = NULL;
        if ( ! openssl_seal($plainData, $sealedData, $ekeys, [$publicKey]))
            throw new \Exception("Encrypt failed: " . openssl_error_string());
        return base64_encode(json_encode([base64_encode($ekeys[0]), base64_encode($sealedData)]));
    }

    public function ssl_decrypt ($encData, $privateKeyFile) {
         if ( ! file_exists($privateKeyFile))
            throw new \Exception("Private Key File $privateKeyFile not found");
        $privateKey = openssl_get_privatekey(file_get_contents($privateKeyFile));
        $input = json_decode(base64_decode($encData), true);
        if ( ! $input)
            throw new \Exception("Invalid json message format");

        if ( ! openssl_open(base64_decode($input[1]), $decryptedData, base64_decode($input[0]), $privateKey))
            throw new \Exception("Decrypt failed: " . openssl_error_string());
        return $decryptedData;
    }
}