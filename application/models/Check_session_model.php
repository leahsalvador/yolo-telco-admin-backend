<?php
class Check_session_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
}