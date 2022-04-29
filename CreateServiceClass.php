<?php

class CreateServiceClass {
    public function s_createClassHeader($className){
        return 'class ' . ucfirst($className) . 'Service {';

    }

    public function s_createGetAllMethod($className){
        return 'public function get' . $className . 's(){
            $rows = $this->select("SELECT * FROM ' . $className . '");
            return $rows;
            }';
    }

    public function s_createGet($className, $primaryKey){
        return 'public function get' . $className . '($' . $primaryKey .'){
            $rows = $this->select("SELECT * FROM ' . $className . ' WHERE ' . $className . 'Id = ?", ["i", $'. $primaryKey . ']);
            return $rows;
            }';
    }

    public function s_createEndOfClass(){
        return '}';
    }

}
?>