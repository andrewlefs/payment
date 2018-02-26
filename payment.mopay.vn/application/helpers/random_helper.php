<?php

function randomExcept($begin, $end, $except) {
    $init = range($begin, $end);
    $result = array_diff($init, $except);
    $result = array_merge($result, array());
    $i = rand(1, count($result));
    return !isset($result[$i - 1]) ? false : $result[$i - 1];
}

?>
