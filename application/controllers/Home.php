<?php
class Home extends CI_Controller {
    public function index(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('home');
        $this->load->view('footer');
    }
    public function login(){
        $nombre = $this->input->post('nombre');
        $pass = $this->input->post('pass');
        $where = [
            'nombre'=>$nombre,
            'pass'=>md5($pass)
        ];
        $this->db->where($where);
        $resultado = $this->db->get('usuarios');
        $num = $resultado->num_rows();
        if($num == 0){
            //no encontro el login
            $this->load->view('header');
            $data = [
                'sms'=>'Usted no es Administrador',
                'tipo'=>'error'
            ];
            $this->load->view('navbar',$data);
            
            $this->load->view('home');
            $this->load->view('footer');
        }else{
            $rest = $resultado->result_array();
            $data = [
                'username'=>$rest[0]['nombre'],
                'login'=>true
            ];
            $this->session->set_userdata($data);
            //echo $this->session->userdata('username');
            $this->load->view('header');
            $this->load->view('navbar');
            $this->load->view('home');
            $this->load->view('footer');
            $base_url = base_url();
            header("Location: $base_url");
        }
    }

    public function reg(){
        $nombre = $this->input->post('nombre');
        $pass = $this->input->post('pass');
        $pass1 = $this->input->post('pass1');
        if ($pass==$pass1) {
            $users = array(
                'nombre' => $nombre,
                'pass' => md5($pass), );
            $this->db->insert('usuarios', $users);
            $base_url = base_url();
            header("Location: $base_url");
        }else{
            
            $base_url = base_url();
            header("Location: $base_url");
        }
    }
    public function visitas(){
        $id= $this->input->post('id');
        $this->db->where('id',$id);
        $direccion= $this->input->post('direccion');
        $visitas= $this->input->post('visitas');
        $url= "../docs/".$direccion;
        $datos = array (
            'visitas' => $visitas+1,
           );
        $this->db->update('docs', $datos);
        $url;
        header("Location: $url");
    }    
    public function logout(){
        $this->session->sess_destroy();
        $base_url = base_url();
        header("Location: $base_url");
    }
    public function pedido(){
        $pedido['status'] = 0;
        $nombre = $this->input->post('nombre');
        $link = $this->input->post('link');
        $idioma = $this->input->post('idioma');
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
        echo json_encode($pedido);
        }
           
    }
    public function qos(){
        $qos['status'] = 0;
        $nombre = $this->input->post('nombre');
        $texto = $this->input->post('texto');
        $ip = $this->input->ip_address();
        if($nombre == '' || $texto == ''){
            $pedido['sms'] = 'Complete todos los campos';

        }else{
        
        $datos = array (
            'nombre' => $nombre,
            'texto' => $texto,
            'ip' => $ip,
           );
        $this->db->insert('qos', $datos);
        $qos['status'] = 200;
        echo json_encode($qos);
        }
           
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
   
    
}
