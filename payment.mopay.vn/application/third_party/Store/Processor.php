<?php

class Store_Processor {

    public function execute($db) {
        $params = get_class_vars($this->object_name);
        unset($params['store_name'], $params['object_name']);
        if (empty($params) === FALSE) {
            foreach ($params as $k => $v) {
                $params[$k] = $this->{$k};
            }
            $array = array_fill(0, count($params), '?');
            $args = implode(',', $array);
            $query = "CALL {$this->store_name}($args)";
            $result = $db->query($query, $params);
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
        return FALSE;
    }
}