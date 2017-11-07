<?php
namespace nn;
class node{
    private static $node_id;
    public function __construct(\nn\network &$network,array $prev_layer=[],$bias=0,$activation_function="sigmoid"){
        $this->master=$network;
        $this->weights=[];
        
        $this->bias=$bias=="auto"?self::get_initilize_value():$bias;
        $this->activation_function=$activation_function;
        $this->value=null;
        $this->_id=self::get_id();
        $this->activated=false;
        $this->outbound=[];
        foreach($prev_layer as &$node){
            $this->weights[]=new edge($node,self::get_initilize_value(),$this);
            
        }
        $this->delta=0;
        
    }
    public function activate(){
        if($this->weights==[]||$this->activated){
            return $this->value;
        }
        
        $this->value=call_user_func_array(__NAMESPACE__."\\".$this->activation_function,[array_sum(array_merge(array_map(function($x){return $x[0]->activate()*$x[1];},$this->weights),[$this->bias]))]);
        $this->activated=true;
        return $this->value;
    }
    public function bind(edge &$link){
        $this->outbound[]=&$link;
    }
    public function learn(){
        foreach($this->weights as &$inbound_weight){
            $inbound_weight[1]-=$this->master->learning_rate*$this->delta*$inbound_weight[0]->value;
        }
        $this->bias-=$this->delta;
    }
    public function backpropogate($prev=null){
        //log derive from oudbound line
        $this->prev_deriv[]=$prev;
        //wait until we recieve all our derivatives otherwise just log them
        if(count($this->prev_deriv)<count($this->outbound)&&$this->outbound!=[]){
            return null;
        }
        //get there sum
       if($this->outbound!=[]){
            $prev=array_sum(array_map(function($edge){
                return $edge[2]->delta*$edge[1];
            },$this->outbound));
       }
       
       
        
        /**
         * [x]=neuron
         * ----->=connection
         * [a] ----- W ----> [b]
         * b=wa
         * db/da=w
         */
        //Work out rate of change of error in relation t
        
        $active_deriv=call_user_func_array(__NAMESPACE__."\\".$this->activation_function."_diff",[$this->value]);
        
        $bias_deriv=$prev*$active_deriv;
        //Update bias based off current deriv.
        $this->delta=$bias_deriv;
        foreach($this->weights as &$weight){
            //given $bias_deriv = $error_gradient
            //$weight_deriv=$bias_deriv*$weight[0]->value;
            //$value_deriv=$prev*$weight[1];
            $weight[0]->backpropogate(1);
            //$weight[1]+=$weight_deriv*$this->master->learning_rate*$weight[0]->value;
        }
        //$this->bias-=$bias_deriv*$this->master->learning_rate;
        /*
        foreach($this->weights as $weight){
            $current=$prev*$weight[1];
            $current=call_user_func_array(__NAMESPACE__."\\".$this->activation_function."_diff",[$weight[1]]);
        }*/

    }
    private static function get_initilize_value(){
        return ((mt_rand() / mt_getrandmax())*2)-1;
    }
    private static function get_id(){
        self::$node_id++;
        return self::$node_id;
    }
}