<?php
class Users_tasks_model extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    //==================================================================================================

    function get_all_sites()
    {
        $this->db->select('site_name, site_id');
        $this->db->from('sites');
        $this->db->order_by('site_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function get_site_search_result($search_value)
    {
        $query = $this->db->query("SELECT * FROM `sites`
        WHERE sites.site_name
        LIKE '%$search_value%'");
        return $query->result();
    }

    function get_all_tasks_assigned()
    {
        $query = $this->db->query("SELECT 
        assigned_task.assigned_task_id,
        assigned_task.user_id,
        assigned_task.team_id,
        assigned_task.assigned_task_id,
        sites.site_name,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name,
        video.played,video.task_id as video_task_id,
        video.request,
        video.video_id,
        site_tasks.* 
        FROM site_tasks 

         LEFT JOIN assigned_task
         ON site_tasks.site_id = assigned_task.assigned_site_id 

         LEFT JOIN users 
         ON assigned_task.user_id = users.user_id 

         LEFT JOIN team 
         ON assigned_task.team_id = team.team_id 
         
         LEFT JOIN sites 
         ON assigned_task.assigned_site_id = sites.site_id

         LEFT JOIN video
         ON site_tasks.site_task_id = video.task_id
         
         ORDER BY assigned_site_id DESC

         ");


        return $query->result();
    }

    function get_search_site_name_result($search_site_name)
    {
        $query = $this->db->query("SELECT 
        assigned_task.assigned_task_id,
        assigned_task.user_id,
        assigned_task.team_id,
        assigned_task.assigned_task_id,
        sites.site_name,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name,
        video.played,video.task_id as video_task_id,
        video.request,
        video.video_id,
        site_tasks.* 
        FROM site_tasks 

         LEFT JOIN assigned_task
         ON site_tasks.site_id = assigned_task.assigned_site_id 

         LEFT JOIN users 
         ON assigned_task.user_id = users.user_id 

         LEFT JOIN team 
         ON assigned_task.team_id = team.team_id 
         
         LEFT JOIN sites 
         ON assigned_task.assigned_site_id = sites.site_id

         LEFT JOIN video
         ON site_tasks.site_task_id = video.task_id
         WHERE sites.site_name
         LIKE '%$search_site_name%'");
        return $query->result();
    }
}
