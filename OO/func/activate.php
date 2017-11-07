<?php
namespace nn;
function sigmoid($x){
    return 1/(1+exp(-$x));
}
function sigmoid_diff($x){
    return $x*(1-$x);//sigmoid($x)*(1-sigmoid($x));
}