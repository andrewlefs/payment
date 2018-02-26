<?php

class MEAPI_Config_Mopay {

    public static function blackbox() {
        return array(
            'url' => 'http://blackbox.mopay.vn/',
            'app' => 'deposit.mopay.vn',
            'secret' => 'DMSEVGS3FCCNJXI3'
        );
    }

    public static function wallet() {
        if (ENVIRONMENT == 'development') {
            return array(
                'url' => 'http://wallet.mopay.dev/',
                'app' => 'mopay',
                'secret' => 'DMSEVGS3FCCNJXI3'
            );
        } else {
            return array(
                'url' => 'http://wallet.mopay.vn/',
                'app' => 'mobo',
                'secret' => 'DMSEVGS3FCCNJXI3'
            );
        }
    }

    public static function withdraw_secret() {
        return 'xYBEHrZBT6p4TN';
    }
}
