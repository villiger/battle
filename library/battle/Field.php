<?php
namespace Battle
{

    class Field 
    {

        const FIELD_SIZE = 6;
        private $field = array();

        public function __construct(){
        }

        public function generate(){
            for($i = 0; $i < $this::FIELD_SIZE; $i++){
                $this->field[$i] = array();
                for($j = 0; $j < $this::FIELD_SIZE; $j++){
                    $this->field[$i][$j] = "Cell";
                }
            }
        }

        public function get(){
            return $this->field;
        }

        public function load($fieldContent){
            $this->field = $fieldContent;
        }
    }
}