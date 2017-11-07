<?php

namespace nn;
class network{
    public function __construct(array $structure, float $learning_rate=0.75){
        $this->layers=[];
        #3d matrix
        #D1 layer
        #D2 nodes on layer
        #D3 Weights to next layer (each item in index references ordered connection to next layer)
        for($layer=0;$layer<count($structure)-1;$layer++){
            $this->layers[]=[];
            for($node=0;$node<$structure[$layer];$node++){
                $this->layers[$layer][$node]=[];
                for($weight=0;$weight<$structure[$layer+1];$weight++){
                    $this->layers[$layer][$node][]=$this->get_initilize_value();
                }
            }
        }
        $this->layers[]=array_map(function($x){return [];},range(1,end($structure)));//Add null weights for the end layer
        $this->values=[];
        $this->learning_rate=$learning_rate;

    }
    public function activate(array $input_vector){
        $this->values=[$input_vector];
        
        for($layer=1;$layer<count($this->layers);$layer++){
            //through each layer
            for($node=0;$node<count($this->layers[$layer]);$node++){
            //and each node
                $total=0;
                //look to the last layer
                for($weight=0;$weight<count($this->layers[$layer-1]);$weight++){
                    $total+=$this->layers[$layer-1][$weight][$node]*$this->values[$layer-1][$weight];
                }
                $this->values[$layer][$node]=$this->sigmoid($total);//squish our result with the sigmoid function
            }
        }
        $to_return=end($this->values);
        reset($this->values);
        return $to_return;
    }
    public function backpropogate(array $expected){
        $this->errors=[];
        #go back through array
        $layer_count=count($this->layers)-1;
        //CALCULATE ERROR GRADIENTS
        for($layer=$layer_count;$layer>0;$layer--){
            $focused_layer=$this->layers[$layer];
            for($node=0;$node<count($focused_layer);$node++){
                if($layer==$layer_count){
                    //if last layer
                    //ai = activation on layer I
                    //bi = Before activation on layer I
                    //Calculate de/dai*dai/dbi
                    $this->errors[$layer][$node]=$this->square_error_deriv($expected[$node],$this->values[$layer][$node])*$this->sigmoid_diff($this->values[$layer][$node]);
                    //
                }
                else{//if hidden or other layer
                    $total_error=0;//sum total errors for previous layer by going -> one layer
                    for($error_target=0;$error_target<count($layer+1);$error_target++){
                        $total_error+=$this->errors[$layer+1][$error_target]*$this->layers[$layer][$node][$error_target];//$layer from node $layer to node $error target
                        //May need to multiply by sigmoid diff here
                    }
                    $total_error*=$this->sigmoid_diff($this->values[$layer][$node]);
                    //save error
                    $this->errors[$layer][$node]=$total_error;
                }
            }
        }
        //LEARN 
        for($layer=$layer_count;$layer>0;$layer--){
            $focused_layer=$this->layers[$layer];
            for($node=0;$node<count($focused_layer);$node++){
                for($weight=0;$weight<count($this->layers[$layer][$node]);$weight++){
                    //get error from prior layer
                    $error=$this->errors[$layer+1][$weight];
                    //Grab activation from this layer (focsed node)
                    $active_of_prev=$this->values[$layer][$node];
                    
                    //apply negative gradient to weights
                    $this->layers[$layer][$node][$weight]-=$error*$active_of_prev*$this->learning_rate;
                }
            }
        }
    }
    public function epoch(array $input_vector,array $output_vector){
        $result=$this->activate($input_vector);
        $SE=0;
        for($output=0;$output<count($output_vector);$output++){
            $SE+=$this->squared_error($result[$output],$output_vector[$output]);
        }
        $this->backpropogate($output_vector);
        
        return $SE;
    }
    private function square_error_deriv($expected,$real){
        return $real-$expected;
    }
    private function squared_error($expected,$real){
        return 0.5*pow($real-$expected,2);
    }
    private static function get_initilize_value(){
        return ((mt_rand() / mt_getrandmax())*2)-1;
    }
    private function sigmoid($x){
        return 1/(1+exp(-$x));
    }
    private function sigmoid_diff($x){
        return $x*(1-$x);//sigmoid($x)*(1-sigmoid($x));
    }
    
}
   
