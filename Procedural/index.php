<?php
require_once(__DIR__."/classes/network.php");
$network=new \nn\network([2,2,2,1]);
set_time_limit(9001); 
echo("<pre>");
for($i=0;$i<1000000;$i++){
    $TSE=0;
    foreach([
        [[0,0],[0]],
        [[0,1],[1]],
        [[1,0],[1]],
        [[1,1],[0]]
    ] as $test_vector){
        $TSE+=$network->epoch($test_vector[0],$test_vector[1]);
        
    }
    //print_r($TSE);
    //echo("<br/>");
}
//var_dump($network->activate([0.6]));
//var_dump($network);
foreach([
    [[0,0],[0]],
    [[0,1],[0]],
    [[1,0],[0]],
    [[1,1],[1]]
] as $test_vector){
    print_r($network->activate($test_vector[0]));
    echo("<br/>");
}
echo("</pre>");