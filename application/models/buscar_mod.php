<?php
class buscar_mod extends CI_Model {
    public function buscar(){
       $query = $this->db->get('docs');
        return $query
    }
    
}