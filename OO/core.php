<?php
require_once(__DIR__."/class/node.php");
require_once(__DIR__."/class/network.php");
require_once(__DIR__."/class/link.php");
require_once(__DIR__."/func/activate.php");
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
ini_set('xdebug.max_nesting_level', 200);
srand(time());
$x=new \nn\network([2,2,2,1]);
echo "<pre>";
//var_dump($x);
echo "</pre>";
error_reporting(E_ALL); ini_set('display_errors', 1);
//print_r($x->activate([1,2,2]));
foreach(
    [
        [[1,1],[0]],
        [[1,0],[1]],
        [[0,1],[1]],
        [[0,0],[0]]
    ]
    as $y
    ){
        
       print_r($y[0]);
       print_r($x->activate($y[0]));
        echo("<br/>");
    }
set_time_limit(9001); 
foreach(range(1,100000) as $k){
    $mse=0;
    foreach(
        [
            [[1,1],[0]],
            [[1,0],[1]],
            [[0,1],[1]],
            [[0,0],[0]]
        ]
        as $y
        ){
            $mse+=$x->epoch($y[0],$y[1]);
            
        }
        //echo("<br/>");
        //echo $mse/2;
}

foreach(
    [
        [[1,1],[1]],
        [[1,0],[0]],
        [[0,1],[0]],
        [[0,0],[0]]
    ]
    as $y
    ){
       print_r($y[0]);
       print_r($x->activate($y[0]));
        echo("<br/>");
    }