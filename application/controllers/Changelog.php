<?php
class Changelog extends CI_Controller {
    public function index(){
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('changelog');
        $this->load->view('footer');
        }
    }

