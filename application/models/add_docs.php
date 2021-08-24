<?php
class add_docs extends CI_Model {
    
    public $ret = array();
    //esto es un upload q yo ise viejo ya...fue la unica ves q use esa libreria
    function upload_file($file_upload,$nombre,$desc,$dir,$idiomaa,$tipo){
        $ret['status'] = 0;
        if($nombre == '' || $desc == '' || $dir == '' || $idiomaa == '' || $tipo == ''){
            $ret['sms'] = 'Complete todos los campos';
        }else{
            
            $config['upload_path'] = './img/';
            $config['allowed_types'] = 'gif|jpg|png|pdf|docx|rar';
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('img_docs')){
                $ret['sms'] = $this->upload->display_errors();
            }else{
                $datos = array('upload_data' => $this->upload->data());
                $adj = $this->upload->data('file_name'); 
                $datos = array (
                    'nombre' => $nombre,
                    'descripcion' => $desc,
                    'imagen' => $adj,
                    'direccion' => $dir,
                    'idioma' => $idiomaa,
                    'tipo' => $tipo,
                   );
                $this->db->insert('docs', $datos);
                $ret['status'] = 200;
            }
        }
        echo json_encode($ret);
    }
}