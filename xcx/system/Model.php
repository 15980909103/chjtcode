<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author admin
 */
class Model {
  

    protected $table;

    public function setTable($name) {
        $table=new Query();
        $this->table=  $table->Name($name);
        
    }

}
