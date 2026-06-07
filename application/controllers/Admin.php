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
        if ( ! $this->require_login(TRUE)) {
            return;
        }

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
        if ( ! $this->require_login()) {
            return;
        }

        $nombre = $this->input->post('nombre', TRUE);
        $this->db->delete('pedidos', array ('nombre' => $nombre)); 
        redirect('/admin');
        
    }
    public function deldoc(){
        if ( ! $this->require_login()) {
            return;
        }

        $nombre = $this->input->post('nombre', TRUE);
        $this->db->delete('docs', array ('nombre' => $nombre)); 
        redirect('/admin');      
    }
    public function delqos(){
        if ( ! $this->require_login()) {
            return;
        }

        $id = (int) $this->input->post('id');
        $this->db->delete('qos', array ('id' => $id)); 
        redirect('/admin');      
    }

    private function require_login($json = FALSE){
        if ($this->session->userdata('username')) {
            return TRUE;
        }

        if ($json) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('status' => 401, 'sms' => 'No autorizado')));
            return FALSE;
        }

        redirect('/');
        return FALSE;
    }
}
