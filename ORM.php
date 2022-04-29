<?php
  function getTableNames($pest){
    $jsonResponse = $pest->get('/tables/list');
    //tableArray is an array of arrays
    $tableArray = json_decode($jsonResponse,true);
    $arr = Array();
    foreach ($tableArray as $table){
      $arr[] = $table["table_name"];
    }
    return $arr;
  }
  function getColumnsForTable($pest, $tableName){
    $jsonResponse = $pest->get('/columns/list/' . $tableName);
    return json_decode($jsonResponse,true);
  }

  function getFKsForTable($pest, $tableName){
    $jsonResponse = $pest->get('/fks/list/' . $tableName);
    return json_decode($jsonResponse,true);

  }




function main(){
  require_once('pest.php');
  //this creates a rest client
  $pest = new Pest('http://appthero.me/index.php');
  
  $tableArr = getTableNames($pest);

  require_once('CreateClass.php');
  require_once('CreateServiceClass.php');

  $createClass = new CreateClass;
  $createServiceClass = new CreateServiceClass;
  
  foreach ($tableArr as $tableName){
      echo "\n\n\n\n\n";

      echo $tableName;
      //create table class for this table

      $classfile = fopen("{$tableName}.php", "w") or die("Unable to open class file!");
      fwrite($classfile, "<?php\n");
      fwrite($classfile, $createClass->createClassHeader($tableName));
      fwrite($classfile, "\n");

      $classServiceFile = fopen("{$tableName}Service.php", "w") or die("Unable to open service file!");
      fwrite($classServiceFile, "<?php\n");
      fwrite($classServiceFile, 'require_once PROJECT_ROOT_PATH."/Model/Database.php";');
      fwrite($classServiceFile, "\n");
      fwrite($classServiceFile, $createServiceClass->s_createClassHeader($tableName));
      fwrite($classServiceFile, "\n");
      fwrite($classServiceFile, $createServiceClass->s_createGetAllMethod($tableName));
      fwrite($classServiceFile, "\n");

      $FKsArray = getFKsForTable($pest, $tableName);

      $columnArray = getColumnsForTable($pest, $tableName);
  
      //first declare all properties
      foreach ($columnArray as $column){
        
        $columnName = $column["COLUMN_NAME"];
        $columnKey = $column["COLUMN_KEY"];

        //replace column name with associated object name (removes "Id")
        if ($columnKey == 'MUL'){
          fwrite($classfile, $createClass->createProperty(str_replace("Id", "", $columnName)));
          fwrite($classfile, "\n");
        } else{
          fwrite($classfile, $createClass->createProperty($columnName));
          fwrite($classfile, "\n");
        }
        if ($columnKey == 'PRI'){
          fwrite($classServiceFile, $createServiceClass->s_createGet($tableName, $columnName));
          fwrite($classServiceFile, "\n");


        } 

        

      }
      //next write getters and settrs for each, after all properties are declared 
      foreach ($columnArray as $column){
        $columnName = $column["COLUMN_NAME"];
        if ($columnKey == 'MUL'){
          
          $columnName = str_replace("Id", "", $columnName);
        }
        fwrite($classfile, $createClass->createGetter($columnName));
        fwrite($classfile, "\n");
        fwrite($classfile, $createClass->createSetter($columnName));
        fwrite($classfile, "\n");

        // $dataType = $column["DATA_TYPE"];
        // $columnType = $column["COLUMN_TYPE"];
        // $extra = $column["EXTRA"];
        
      }
      //finish writing class service file
      fwrite($classServiceFile, $createServiceClass->s_createEndOfClass($tableName));
      fwrite($classServiceFile, "\n");
      fwrite($classServiceFile, "?>");
      fclose($classServiceFile);

      //finish writing class file
      fwrite($classfile, $createClass->createEndOfClass($tableName));
      fwrite($classfile, "\n");
      fwrite($classfile, "?>");
      fwrite($classfile, "\n");
      fclose($classfile);      
    }
}
main();
  
?>