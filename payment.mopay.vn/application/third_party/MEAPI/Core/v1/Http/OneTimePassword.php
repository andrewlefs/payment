<?php

namespace Misc\Http;

class OneTimePassword {

    const DefaultCodeLength = 6;
    const WAITING = 30;
    const DefaultKey = 'DZHBZJI7TPOOE6QE';

    protected $length;
    protected $waiting;

    protected function _base32Decode($secret) {
        if (empty($secret))
            return '';

        $base32chars = OneTimePassword::_getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);

        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues))
            return false;
        for ($i = 0; $i < 4; $i++) {
            if ($paddingCharCount == $allowedValues[$i] &&
                    substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i]))
                return false;
        }
        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = "";
        for ($i = 0; $i < count($secret); $i = $i + 8) {
            $x = "";
            if (!in_array($secret[$i], $base32chars))
                return false;
            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y : "";
            }
        }
        return $binaryString;
    }

    protected function _getBase32LookupTable() {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '='  // padding char
        );
    }

    public function getCode($secretkey = null, $timeSlice = null, $waiting = null) {
        
        if ($timeSlice === null) {
            $timeSlice = time();
        }
        
         if ($waiting === null) {
            $waiting = $this->getWaiting();
        }
        
        $timeSlice = floor($timeSlice / $waiting);
        if (empty($secretkey)) {
            $secretkey = OneTimePassword::_base32Decode(OneTimePassword::DefaultKey);
        } else {
            $secretkey = OneTimePassword::_base32Decode($secretkey);
        }


        // Pack time into binary string
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);
        // Hash it with users secret key
        $hm = hash_hmac('SHA1', $time, $secretkey, true);

        // Use last nipple of result as index/offset
        $offset = ord(substr($hm, -1)) & 0x0F;

        // grab 4 bytes of the result
        $hashpart = substr($hm, $offset, 4);
        // Unpak binary value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        // Only 32 bits
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, $this->getCodeLength());
        return str_pad($value % $modulo, $this->getCodeLength(), '0', STR_PAD_LEFT);
    }

    public function setCodeLength($length) {
        $this->length = $length;
    }

    public function getCodeLength() {
        return $this->length == null ? self::DefaultCodeLength : $this->length;
    }

    public function setWaiting($waiting = null) {        
        $this->waiting = $waiting;       
    }

    public function getWaiting() {       
        return $this->waiting == null ? self::WAITING : $this->waiting;
    }

    /**
     * Avoid parse_str() for HHVM compatibility
     * This implementation is not a complete sobstitute, but covers all the
     * requirements of the Facebook Graph Cursor.
     *
     * @see hhvm.hack.disallow_dynamic_var_env_funcs
     * @param $query_string
     * @return array
     */
    public static function parseUrlQuery($query_string) {
        $query = array();
        $pairs = explode('&', $query_string);
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $query[$key] = $value;
        }

        return $query;
    }

    static protected $instance;

    /**
     * 
     * @return OneTimePassword
     */
    static function getInstance() {
        
        if (self::$instance == null) {
            self::$instance = new OneTimePassword();
        }       
        return self::$instance;
    }

    static public function reset() {
        if (self::$instance != null) {
            self::$instance = null;
        }
        return self::$instance;
    }

}
