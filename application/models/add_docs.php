<?php
class add_docs extends CI_Model {
    
    public $ret = array();
    function upload_file($file_upload,$nombre,$desc,$dir,$idiomaa,$tipo){
        $ret = array('status' => 0);
        if($nombre == '' || $desc == '' || $dir == '' || $idiomaa == '' || $tipo == ''){
            $ret['sms'] = 'Complete todos los campos';
        }else{
            
            $config['upload_path'] = './public/img/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|svg|webp';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;
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
        $this->output->set_content_type('application/json')->set_output(json_encode($ret));
    }
}
