<?php
class Home extends CI_Controller {
    public function index(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('home');
        $this->load->view('footer');
    }
    public function login(){
        $nombre = trim((string) $this->input->post('nombre', TRUE));
        $pass = (string) $this->input->post('pass');

        $resultado = $this->db->get_where('usuarios', array('nombre' => $nombre), 1);
        $usuario = $resultado->row_array();

        if ( ! $usuario || ! $this->password_matches($pass, $usuario['pass'])){
            $this->load->view('header');
            $data = [
                'sms'=>'Usted no es Administrador',
                'tipo'=>'error'
            ];
            $this->load->view('navbar',$data);
            
            $this->load->view('home');
            $this->load->view('footer');
        }else{
            if (password_get_info($usuario['pass'])['algo'] === 0) {
                $this->db->where('id', $usuario['id']);
                $this->db->update('usuarios', array('pass' => password_hash($pass, PASSWORD_DEFAULT)));
            }

            $data = [
                'username'=>$usuario['nombre'],
                'login'=>true
            ];
            $this->session->set_userdata($data);
            redirect('/');
        }
    }

    public function reg(){
        $nombre = trim((string) $this->input->post('nombre', TRUE));
        $pass = $this->input->post('pass');
        $pass1 = $this->input->post('pass1');
        $users_count = $this->db->count_all('usuarios');
        $can_register = ($users_count === 0) || (bool) $this->session->userdata('username');

        if ($can_register && $nombre !== '' && $pass !== '' && $pass === $pass1) {
            $exists = $this->db->get_where('usuarios', array('nombre' => $nombre), 1)->row_array();
            if ($exists) {
                redirect('/');
                return;
            }

            $users = array(
                'nombre' => $nombre,
                'pass' => password_hash($pass, PASSWORD_DEFAULT),
            );
            $this->db->insert('usuarios', $users);
        }
        redirect('/');
    }
    public function visitas(){
        $id= (int) $this->input->post('id');
        $this->db->where('id',$id);
        $direccion= trim((string) $this->input->post('direccion', TRUE));
        $visitas= (int) $this->input->post('visitas');
        $url = preg_match('#^https?://#i', $direccion)
            ? $direccion
            : base_url('docs/'.ltrim($direccion, '/\\'));
        $datos = array (
            'visitas' => $visitas+1,
           );
        $this->db->update('docs', $datos);
        $url;
        redirect($url);
    }    
    public function logout(){
        $this->session->sess_destroy();
        redirect('/');
    }
    public function pedido(){
        $pedido = array('status' => 0);
        $nombre = trim((string) $this->input->post('nombre', TRUE));
        $link = trim((string) $this->input->post('link', TRUE));
        $idioma = trim((string) $this->input->post('idioma', TRUE));
        if($nombre == '' || $link == '' || $idioma == ''){
            $pedido['sms'] = 'Complete todos los campos';
        }else{
            $datos = array (
                'nombre' => $nombre,
                'link' => $link,
                'idioma' => $idioma,
            );
            $this->db->insert('pedidos', $datos);
            $pedido['status'] = 200;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($pedido));
           
    }
    public function qos(){
        $qos = array('status' => 0);
        $nombre = trim((string) $this->input->post('nombre', TRUE));
        $texto = trim((string) $this->input->post('texto', TRUE));
        $ip = $this->input->ip_address();
        if($nombre == '' || $texto == ''){
            $qos['sms'] = 'Complete todos los campos';
        }else{
            $datos = array (
                'nombre' => $nombre,
                'texto' => $texto,
                'ip' => $ip,
            );
            $this->db->insert('qos', $datos);
            $qos['status'] = 200;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($qos));
    }
    public function buscar(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('busqueda');
        $this->load->view('footer');
    }
    public function idioma(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('idioma');
        $this->load->view('footer');
    }
    public function tipo(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('tipo');
        $this->load->view('footer');
    }
   
    private function password_matches($plain, $stored){
        if (password_get_info($stored)['algo'] !== 0) {
            return password_verify($plain, $stored);
        }

        return hash_equals((string) $stored, md5($plain));
    }
    
}
