<?php

if (!function_exists('get_lang')) {

    function get_lang($msg) {
        include_once APPPATH . 'third_party/MEAPI/Languages/language_default.php';
        return $language['default'][$msg] ? $language['default'][$msg] : FALSE;
    }

}

if (!function_exists('get_client_ip')) {

    function get_client_ip() {
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

if (!function_exists('make_order_id')) {

    function make_order_id() {
        return strtolower(uniqid());
    }

}

if (!function_exists('arr2filter_query')) {

    function arr2filter_query($arr) {
        if (is_array($arr) === FALSE || empty($arr) === TRUE) {
            return false;
        }
        foreach ($arr as $key => $value) {
            if (preg_match('/filtervalue/is', $key)) {
                $num = str_replace('filtervalue', '', $key);
                $where_like[$num]['value'] = trim($value);
            }
            if (preg_match('/filterdatafield/is', $key)) {
                $num = str_replace('filterdatafield', '', $key);
                $where_like[$num]['col'] = trim($value);
            }
        }
        if (empty($where_like) === TRUE) {
            return FALSE;
        }
        return $where_like;
    }

}

if (!function_exists('json_filter_query')) {

    function format_array_query($arr = array()) {
        $where_like = array();
        if (is_array($arr) === FALSE || empty($arr) === TRUE) {
            return false;
        }

        foreach ($arr as $key => $value) {
            if (!empty($value)) {
                if ($key == 'service_id') {
                    foreach (MEAPI_Config_App::service() as $k => $val) {
                        if ($value == $val) {
                            array_push($where_like, array(
                                'col' => $key,
                                'value' => $k
                            ));
                        }
                    }
                    unset($arr['service_id']);
                } else {
                    array_push($where_like, array(
                        'col' => $key,
                        'value' => $value
                    ));
                }
            }
        }
        if (empty($where_like) === TRUE) {
            return FALSE;
        }
        return $where_like;
    }

}

if (!function_exists('arr2filter')) {

    function arr2filter($arr) {
        if (is_array($arr) === FALSE || empty($arr) === TRUE) {
            return false;
        }
        foreach ($arr as $key => $value) {
            if (preg_match('/filtervalue/is', $key)) {
                $num = str_replace('filtervalue', '', $key);
                $data[$num]['value'] = trim($value);
            }
            if (preg_match('/filterdatafield/is', $key)) {
                $num = str_replace('filterdatafield', '', $key);
                $data[$num]['code'] = trim($value);
            }
        }
        if (empty($data) === TRUE) {
            return FALSE;
        }
        foreach ($data as $key => $value) {
            if (count($value) === 2) {
                $result[$value['code']] = $value['value'];
            }
        }
        return $result;
    }

}

if (!function_exists('jqwidgets_convert_datetime')) {

    function jqwidgets_convert_datetime($arr, $col) {
        if (is_array($arr) === FALSE || empty($arr) === TRUE || empty($col) === TRUE) {
            return false;
        }
        foreach ($arr as $key => $value) {
            if ($value['col'] == $col) {
                $time_arr = explode(' ', $value['value']);
                $time_str = strtotime(implode(' ', array_slice($time_arr, 0, 5)));
                if (empty($time_str) === FALSE) {
                    
                }
                $date_str = date('Y-m-d', $time_str);
                $quarter = date('Y', $time_str) . '_' . ceil(date('n', $time_str) / 3);
                $where_time[$key] = array(
                    'value' => $date_str,
                    'quarter' => $quarter,
                    'time' => $time_str,
                    'col' => $col,
                );
            }
        }
        if (empty($where_time) === TRUE) {
            return FALSE;
        }
        return $where_time;
    }

}

if (!function_exists('format_data')) {

    function format_data($var) {
        if (is_numeric($var)) {
            return number_format($var);
        } else {
            if (empty($var) === FALSE) {
                return $var;
            } else {
                return '-';
            }
        }
    }

}

if (!function_exists('deleteDirectory')) {

    function deleteDirectory($dirPath) {
        if (is_dir($dirPath))
            $dirHandle = opendir($dirPath);
        if (!$dirHandle) {
            return false;
        }
        while ($file = readdir($dirHandle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirPath . "/" . $file))
                    unlink($dirPath . "/" . $file);
                else
                    deleteDirectory($dirPath . '/' . $file);
            }
        }
        closedir($dirHandle);
        rmdir($dirPath);
        return true;
    }

}

if (!function_exists('check_ip_local')) {
    function check_ip_local()
    {
        //Remove banking base on IP LIST
        $list_ip = array('203.162.79.124', '115.78.161.134', '127.0.0.1', '115.78.161.124', '192.168.1.5', '14.161.5.226', '14.161.5.226', '118.69.76.21', '113.161.77.69', '113.161.78.101', '118.69.76.212', '115.78.161.88', '203.162.56.175', '123.30.140.179', '203.162.79.103', '203.162.79.104', '203.162.79.105', '203.162.79.118');
        $user_ip = get_client_ip();

        //Not in list IP
        if (in_array($user_ip, $list_ip) == FALSE) {
            return FALSE;
        }
        return TRUE;
    }
}

if( !function_exists('msv_explode') ){
    function msv_explode($channel){
        $channel = explode('|', $channel);
        $msv = $msv_number = $msv_type = $msv_base = '';

        foreach ($channel as $item) {
            if (strpos($item, 'msv') !== FALSE) {
                $msv = $item;
                $explode = explode('_', $msv);
                $msv_number = $explode[1];
                $msv_base = $explode[0].'_'.$explode[1];
                $msv_type = isset($explode[2]) ? $explode[2] : FALSE;
                break;
            }
        }

        //Map old version
        if ($msv_base != FALSE && $msv_type == FALSE) {
            foreach ($channel as $item) {
                $item = strtolower($item);

                $check_array = array('gp', 'appstore');
                if (in_array($item, $check_array)) {
                    $msv_type = 'store';
                    break;
                }

                $check_array = array('file', 'ent');
                if (in_array($item, $check_array)) {
                    $msv_type = 'file';
                    break;
                }
            }
        }

        $result = array(
            'msv' => $msv,
            'msv_base' => $msv_base,
            'msv_type' => $msv_type,
            'msv_number' => (int)$msv_number
        );

        return $result;
    }
}