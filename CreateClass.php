<?php
class CreateClass {

    public function createClassHeader($className){
        return "class " . ucfirst($className) . " {";

    }
    public function createProperty($propertyName){
        return 'private $' . $propertyName . ';';

    }
    public function createGetter($propertyName){
        //after 'return $this-> ...' I removed the '$' because php was giving an error and I looked online and it said the returning variable should not have a $ in front.
        return "public function get" . $propertyName . '(){ return $this->' . $propertyName . '; }';
        
    }
    public function createSetter($propertyName){
        return 'public function set' . $propertyName . '($' . $propertyName . '){ $this->$' . $propertyName . '= $' . $propertyName . '; }';
        
    }
    public function createEndOfClass(){
        return '}';
    }
}

?>