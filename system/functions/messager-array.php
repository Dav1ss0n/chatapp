<?php 

function messagerArray_l2($method, $status) {
    $array = array(
        "Method" => $method,
        "Status" => $status
    );
    return json_encode($array);
}

function messagerArray_l3($parameter, $status, $problem) {
    $array = array(
        "Parameter" => $parameter,
        "Status" => $status,
        "Problem" => $problem
    );
    return json_encode($array);
}
?>