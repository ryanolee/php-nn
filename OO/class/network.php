<?php
namespace nn;
class network{
    public function __construct(array $layers,$learning_rate=0.75){
        $this->layers=[[]];
        for($i=0;$i<$layers[0];$i++){
            $this->layers[0][]=new node($this,[],null);
        }

        for($i=1;$i<count($layers);++$i){
            $this->layers[]=[];
            for($x=0;$x<$layers[$i];$x++){
                $this->layers[$i][]=new node($this,$this->layers[$i-1],"auto");
            }
        }
        $this->learning_rate=$learning_rate;
    }
    public function activate(array $input_vector){

        if(count($input_vector)!=count($this->layers[0])){
            throw new \Exception("Input vector does not match number of input neurons for this network");
        }
        foreach($this->layers as $layer){
            foreach($layer as $node){
                $node->activated=false;
            }
        }
        $i=0;
       // print_r($input_vector);
        foreach($input_vector as $input){
            //normalize

             $input=(max($input_vector)==min($input_vector))?$input:($input-min($input_vector))/(max($input_vector)-min($input_vector));
           //print_r($input);
            //set input
            $this->layers[0][$i]->value=$input;
            $i++;
        }
        
        return array_map(function($item){return $item->activate();},end($this->layers));
        
        
    }
    public function train(array $vectors,float $target_error,int $epochs,string $mode="stochastic"){
        if($mode=="stochastic"){
            $counter=0;
            $total_training_vectors=count($vectors);
            //NOTE inturnal function scope is persisted by closure.
            $selection_function=function()use($counter,$total_training_vectors){if($counter>$total_training_vectors){$counter=0;}return $counter++;};
        }
        $i=1;
        $TSE=[];//Total Squared Error
        $MSE=1;//Mean Square Error
        while($target_error>$MSE&&$epochs>$i){
            $target_vector=$vectors[$selection_function()];//That makes sense right?
            $input_vector=$target_vector[0];
            $output_vector=$target_vector[1];
            array_push($TSE,$this->epoch($input_vector,$output_vector));
            if(count($TSE)>$total_training_vectors){
                array_shift($TSE);
            }
            $MSE=array_sum($TSE)/$total_training_vectors;
            $i++;
        }
    }
    public function epoch(array $input_vector,array $output_vector){
        
        if(count($output_vector)!=count(end($this->layers))){
            throw new \Exception("Output vector does not match number of output neurons");
        }
        reset($this->layers);
        foreach($this->layers as &$layer){
            foreach($layer as &$node){
                $node->prev_deriv=[];
                $node->delta=0;
            }
        }
        
        $res=$this->activate($input_vector);
        $total_square_error=0;
        foreach(array_map(null,$res,$output_vector,end($this->layers))as $error){
    

            if($error==0){
                continue;
            }
            $error_deriv=$error[0]-$error[1];//?-1:1;
            $total_square_error+=pow($error[0]-$error[1],2);
            $error[2]->backpropogate($error_deriv);
            
        }
        reset($this->layers);
        /*foreach(end($this->layers) as &$neuron){
            $neuron->backpropogate(1);
        }*/
        reset($this->layers);
        foreach($this->layers as &$layer){
            foreach($layer as &$node){
                $node->learn();
            }
        }
        
        
        return $total_square_error;//$total_square_error;
    }
   
}