<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Bangkok');

class Users_tasks extends CI_Controller
{

    public function get_all_sites()
    {
        $this->load->model('Users_tasks_model');
        $all_sites_list = $this->Users_tasks_model->get_all_sites();
        echo json_encode($all_sites_list);
    }

    public function get_site_search_result()
    {
        $this->load->model('Users_tasks_model');
        $site_search_result = $this->Users_tasks_model->get_site_search_result($_POST['search_value']);
        echo json_encode($site_search_result);
    }

    public function get_all_tasks_assigned()
    {
        $this->load->model('Users_tasks_model');
        $all_task_assigned = $this->Users_tasks_model->get_all_tasks_assigned();
        echo json_encode($all_task_assigned);
    }

    public function search_site_name()
    {
        $this->load->model('Users_tasks_model');
        $search_site_name_result = $this->Users_tasks_model->get_search_site_name_result($_POST['search_site_name_value']);
        echo json_encode($search_site_name_result);
    }
}
