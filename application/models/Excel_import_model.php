<?php
class Excel_import_model extends CI_Model{
    function __construct(){
        parent::__construct();
		$this->load->database();
    }

    function insert($data){
        $this->db->insert_batch('sites', $data);
    } 

    function delete_null_site_rows(){
        $this->db->where('site_name', '');
        $this->db->delete('sites');
    }
}