<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model("users");
    }

    public function index()
    {
        echo "test";
    }
    public function sample()
    {
        echo "sample";
    }
}//====================controller closing
