<?php

class StoreProcedure {

    static public function call($name, $params, $db) {
        $array = array_fill(0, count($params), '?');
        $args = implode(',', $array);
        $query = "CALL {$name}($args)";

        $result = $db->query($query, $params);
        //echo $db->last_query();
        //if ($name == 'sp_withdraw_insert') {
            //echo $db->last_query();
        //}
        //echo $db->last_query();
        $db->freeDBResource($db->conn_id);
        if (mysql_error()) {
            echo mysql_error();
            exit;
            return FALSE;
        } else {
            return $result;
        }
    }

}
