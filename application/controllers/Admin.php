<?php
class Admin extends CI_Controller {
    public function index(){
        if($this->session->userdata('username')){
            $this->load->view('header');
            $this->load->view('navbar');
            $this->load->view('admin');
            $this->load->view('footer');

        }else{
            redirect('/');
        }

          
    }   
    public function upload(){
        $this->load->model('add_docs');
        $file_upload = $this->input->post('img_docs', TRUE);
        $nombre = $this->input->post('nombre', TRUE);
        $desc = $this->input->post('descripcion', TRUE);
        $dir = $this->input->post('direccion', TRUE);
        $idiomaa = $this->input->post('idiomaa', TRUE);
        $tipo = $this->input->post('tipo', TRUE);
        $this->add_docs->upload_file($file_upload,$nombre,$desc,$dir,$idiomaa,$tipo);

    }
    public function delpet(){
        $nombre = $this->input->post('nombre', TRUE);
        $this->db->delete('pedidos', array ('nombre' => $nombre)); 
        redirect('/admin');
        
    }
    public function deldoc(){
        $nombre = $this->input->post('nombre', TRUE);
        $this->db->delete('docs', array ('nombre' => $nombre)); 
        redirect('/admin');      
    }
    public function delqos(){
        $id = (int) $this->input->post('id');
        $this->db->delete('qos', array ('id' => $id)); 
        redirect('/admin');      
    }
}
