<?php
class Admin extends CI_Controller {
    public function index(){
        if($this->session->userdata('username')){
            $this->load->view('header');
            $this->load->view('navbar');
            $this->load->view('admin');
            $this->load->view('footer');

        }else{
            $base_url = base_url();
            header("Location: $base_url");
        }

          
    }   
    public function upload(){
        $this->load->model('add_docs');
        $file_upload = $this->input->post('img_docs');
        $nombre = $this->input->post('nombre');
        $desc = $this->input->post('descripcion');
        $dir = $this->input->post('direccion');
        $idiomaa = $this->input->post('idiomaa');
        $tipo = $this->input->post('tipo');
        $this->add_docs->upload_file($file_upload,$nombre,$desc,$dir,$idiomaa,$tipo);

    }
    public function delpet(){
        $nombre = $this->input->post('nombre');
        $this->db->delete('pedidos', array ('nombre' => $nombre)); 
        redirect('/admin');
        
    }
    public function deldoc(){
        $nombre = $this->input->post('nombre');
        $this->db->delete('docs', array ('nombre' => $nombre)); 
        redirect('/admin');      
    }
    public function delqos(){
        $id = $this->input->post('id');
        $this->db->delete('qos', array ('id' => $id)); 
        redirect('/admin');      
    }
}