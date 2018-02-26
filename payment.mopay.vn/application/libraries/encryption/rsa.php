<?php

class rsa {

    private $pubkey = '';
    private $prikey = '';

    public function __construct($config) {
        $this->pubkey = $config['pub_key'];
        $this->prikey = $config['pri_key'];
    }

    public function encrypt($data) {
        if (openssl_public_encrypt($data, $encrypted, $this->pubkey))
            $data = base64_encode($encrypted);
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');

        return $data;
    }

    public function decrypt($data) {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->prikey))//file_get_contents(APPPATH . DIRECTORY_SEPARATOR. 'config'.DIRECTORY_SEPARATOR. 'private_key.pem')
            $data = $decrypted;
        else
            $data = '';

        return $data;
    }

}

//$enc=new rsa();
//echo $enc->decrypt("lT0g882VN1879pOHjbFbRGPDT566pttDMIQoexwltt4qgprYfB6523WKv//hyhCG3UrZ4qSRD2BYWwea8RAc5QzoA3MTsiXVwFmlcO4UQ00vRBPyQAOlJnmvjT/9FvsQ68Eo9/qkKPWsCB0n9DjHCFQYyKJGOBM1AynGewKPJcI=");
?>