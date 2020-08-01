<?php
class Site_model extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //------------------------------------------------------------------------------------------------------
    function saveSite($site)
    {
        if ($this->db->insert('sites', $site)) {
            return true;
        } else {
            return false;
        }
    }

    function get_site_and_site_detailes()
    {
        $this->db->select('*');
        $this->db->from('sites');
        $this->db->order_by('site_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function get_site_detailes_by_id($site_id)
    {
        $this->db->where('site_id', $site_id);
        $this->db->select('*');
        $this->db->from('sites');
        $query = $this->db->get();
        return $query->result();
    }
    //-------------------------------------------------------------------------------------------------------
    function delete_sites($site_id)
    {
        $this->db->where('site_id', $site_id);
        $this->db->delete('sites');
    }

    function delete_site_assigned_site($site_id)
    {
        $this->db->where('assigned_site_id', $site_id);
        $this->db->delete('assigned_task');
    }

    function delete_site_tasks_if_the_site_has_been_deleted($site_id)
    {
        $this->db->where('site_id', $site_id);
        $this->db->delete('site_tasks');
    }

    function delete_team_member_task($site_id)
    {
        $this->db->where('site_id', $site_id);
        $this->db->delete('team_member_task');
    }

    //-------------------------------------------------------------------------------------------------------
    function update_sites($site_id, $sites)
    {
        $this->db->where('site_id', $site_id);
        $this->db->update('sites', $sites);
    }
    //-------------------------------------------------------------------------------------------------------
    function get_sites_details($site_id)
    {
        $this->db->select('*');
        $this->db->from('sites');
        $this->db->where('site_id', $site_id);
        $query = $this->db->get();
        return $query->result();
    }
    function get_site_to_export($site_id)
    {
        $this->db->where_in('site_id', $site_id);
        $this->db->select('*');
        $this->db->from('sites');
        $query = $this->db->get();
        return $query->result();
    }




    //======Manage Site Task Status/Evidences===============================================================================================================
    function all_sites()
    {
        $this->db->select('sites.site_id, sites.site_name');
        $this->db->from('sites');
        $query = $this->db->get();
        return $query->result();
    }

    function get_site_task_manage($site_id)
    {
        $query = $this->db->query("SELECT 
        assigned_task.assigned_task_id,
        assigned_task.user_id,
        assigned_task.team_id,
        assigned_task.assigned_task_id,
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

         WHERE site_tasks.site_id = '$site_id'");
        return $query->result();
    }

    function get_completed_Task_even_not_assigned()
    {
        $query = $this->db->query("SELECT * FROM site_tasks");
    }


    function get_the_name_of_user_the_site_was_assigned($site_id)
    {
        $this->db->select('*');
        $this->db->from('assigned_task');
        $this->db->join('users', 'assigned_task.user_id = users.user_id', 'left');
        $this->db->join('team', 'assigned_task.team_id = team.team_id', 'left');
        $this->db->join('sites', 'assigned_task.assigned_site_id = sites.site_id', 'left');
        $this->db->where('assigned_site_id', $site_id);
        $query = $this->db->get();
        return $query->result();
    }




    function get_all_task_that_was_assigned()
    {
        $query = $this->db->query("SELECT * FROM `sites`
        left JOIN assigned_task
        ON sites.site_id = assigned_task.assigned_site_id 
        
        left JOIN users
        ON assigned_task.user_id = users.user_id
        
        left JOIN team
        ON assigned_task.team_id = team.team_id
        
       group by sites.site_id
       
");
        return $query->result();
    }

    //======================================================================

    function search_site_for_adding_video_instruction($search_site)
    {
        $query = $this->db->query("SELECT * FROM `sites`
        left JOIN assigned_task
        ON sites.site_id = assigned_task.assigned_site_id 
        
        left JOIN users
        ON assigned_task.user_id = users.user_id
        
        left JOIN team
        ON assigned_task.team_id = team.team_id
        
        WHERE sites.site_name
        LIKE '%$search_site%'");
        return $query->result();
    }

    function get_site_search_result_for_video_sending($user_id)
    {
        $this->db->select('sites.site_id, sites.site_name');
        $this->db->where('site_id', $user_id);
        $this->db->from('sites');
        $query = $this->db->get();
        return $query->result();
    }


    function get_all_sites_for_sending_vid()
    {
        $query = $this->db->query("SELECT * FROM `sites`
        left JOIN assigned_task
        ON sites.site_id = assigned_task.assigned_site_id 
        
        left JOIN users
        ON assigned_task.user_id = users.user_id
        
        left JOIN team
        ON assigned_task.team_id = team.team_id
        
       group by sites.site_id
       
        ");
        return $query->result();
    }

    function reschedule_task($task_id, $task_date)
    {
        $this->db->where("site_task_id", $task_id);
        $this->db->update("site_tasks", $task_date);
    }
    function getSitesForPdf($site_id)
    {
        $q = $this->db->get_where('sites', array('site_id' => $site_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
}
