<?php

namespace Misc\Http;

use Misc\Security;

abstract class Util_Helper {

    static function formatPath($path) {
        if (empty($path) === true)
            return $path;
        $endCharPath = substr($path, -1);
        if ($endCharPath == '&') {
            return self::formatPath(substr($path, 0, strlen($path) - 1));
        }
        return $path;
    }

    /**
     * 
     * @param type $basepath
     * @param array $params
     * @param type $hash
     * @param type $timeslice
     * @return type
     */
    static function RebuildUrl($basepath, array $params = array(), $hash = "", $timeslice = -1) {
        $parts = parse_url($basepath);
        parse_str($parts['query'], $querys);
        $path = '/';
        if (isset($parts['path']) && !empty($parts["path"])) {
            $path = self::formatPath($parts["path"]);
        }
        $endCharPath = substr($path, -1);
        if ($endCharPath == '&') {
            $path = substr($path, 0, strlen($path) - 1);
        }
        unset($params["token"], $params["otp"]);
        $newOtp = Util::getCode($hash, $timeSlice);
        if (count($querys) > 0) {
            foreach ($querys as $key => $value) {
                if (!isset($params[$key]))
                    $params[$key] = $value;
            }
        }
        $app = $params["app"];
        unset($params["app"]);
        $sourceToken = implode("", $params);
        $reToken = md5($sourceToken . $newOtp . $hash);
        $params["app"] = $app;
        $params["otp"] = $newOtp;
        $params["token"] = $reToken;
        $url = (isset($parts["scheme"]) ? $parts["scheme"] . "://" . $parts["host"] : "")
                . (isset($parts["port"]) ? ":" . $parts["port"] : "")
                . $path . "?" . http_build_query($params);
        return $url;
    }

    static function RebuildExtendUrl($path, array $params = array(), array $extend = array(), $hash = "", $timeslice = -1) {
        //if exits q prepare q
        if (isset($params['q'])) {
            $prepareParams = Security::decrypt($params["q"], $hash);
            $prepareParams["extend"] = $extend;
            $params["q"] = Security::encrypt($prepareParams, $hash);
        } else {
            $params["extend"] = json_encode($extend);
        }
        return static::RebuildUrl($path, $params, $hash);
    }

    static function RebuildRemoveParamUrl($path, array $params = array(), array $extend = array(), $hash = "", $timeslice = -1) {
        //if exits q prepare q
        foreach ($extend as $key => $value) {
            unset($params[$value]);
        }
        if (isset($params["q"])) {
            $prepareParams = Security::decrypt($params["q"], $hash);
            foreach ($extend as $key => $value) {
                unset($prepareParams[$value]);
            }
            $params["q"] = Security::encrypt($prepareParams, $hash);
        }
        return static::RebuildUrl($path, $params, $hash);
    }

    static function sqlInjectionBase($string) {
        //\x00, \n, \r, \, ', ", ,; and \x1a
        $str = strtolower($string);
        $injectionChar = array('\x00', '\n', '\r', '\\', "'", '"', ';', '\x1a');
        foreach ($injectionChar as $key => $value) {
            if (strpos($str, $value) !== false) {
                return false;
            }
        }
        return true;
    }

    static function special_character_remove($str) {
        if (empty($str) == true) {
            return $str;
        }
        if (is_string($str) !== true) {
            return $str;
        }
        $unicode = ' aqwertyuiopsdfghjklzxcvbnm123456789QWERTYUIOPASDFGHJKLZXCVBNMáàảãạăắặằẳẵâấầẩẫậdđeéèẻẽẹêếềểễệiíìỉĩịoóòỏõọôốồổỗộơớờởỡợuúùủũụưứừửữựyýỳỷỹỵAÁÀẢÃẠĂẮẶẰẲẴÂẤẦẨẪẬDĐEÉÈẺẼẸÊẾỀỂỄỆIÍÌỈĨỊOÓÒỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢUÚÙỦŨỤƯỨỪỬỮỰYÝỲỶỸỴ';
        $cloneUnicode = str_split($unicode);
        $acsii = array();
        foreach ($cloneUnicode as $key => $value) {
            $acsii[ord($value)] = ord($value);
        }
        $cloneStr = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $char = mb_substr($str, $i, 1);
            if (isset($acsii[ord($char)]) == true) {
                $cloneStr .= $char;
            }
        }
        return $cloneStr;
    }

}
