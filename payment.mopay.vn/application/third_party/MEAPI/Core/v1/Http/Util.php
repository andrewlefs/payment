<?php

namespace Misc\Http;

abstract class Util {

    const DefaultCodeLength = 6;
    const DefaultKey = 'DZHBZJI7TPOOE6QE';

    static protected function _base32Decode($secret) {
        if (empty($secret))
            return '';

        $base32chars = Util::_getBase32LookupTable();
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

    static protected function _getBase32LookupTable() {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '='  // padding char
        );
    }

    static protected function _getBaseAllLookupTable() {
        return array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', //  7
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', // 15
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x', // 23
            'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9' // 31            
        );
    }

    static public function getShortLink($lenght = 8) {
        $binaryString = "";
        $lookup = self::_getBaseAllLookupTable();
        for ($i = 0; $i < $lenght; $i++) {
            $rand = rand(0, count($lookup));
            $binaryString .= $lookup[$rand];
        }
        return $binaryString;
    }

    static public function getCode($scretkey = null, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        if (empty($scretkey)) {
            $secretkey = Util::_base32Decode(Util::DefaultKey);
        } else {
            $secretkey = Util::_base32Decode($scretkey);
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

        $modulo = pow(10, Util::DefaultCodeLength);
        return str_pad($value % $modulo, Util::DefaultCodeLength, '0', STR_PAD_LEFT);
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

    public static function get_remote_ip() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}
