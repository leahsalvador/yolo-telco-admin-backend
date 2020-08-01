<?php
class Users extends CI_Model
{

    //------------------------------------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //------------------------------------------------------------------------------------------------------

    function add_user($users)
    {
        if ($this->db->insert('users', $users)) {
            return true;
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function get_user($user)
    {
        $q = $this->db->get_where('users', array('email' => $user['email'], 'password' => $user['password']), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function get_all_users()
    {
        $this->db->where("user_group", 1);
        $q = $this->db->get("users");
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function delete_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users');
    }

    //------------------------------------------------------------------------------------------------------

    function update_user($user_id, $update_data)
    {
        $this->db->where("user_id", $user_id);
        $this->db->update("users", $update_data);
    }

    //------------------------------------------------------------------------------------------------------
    function get_user_location($user_id)
    {
        $this->db->where("user_id", $user_id);
        $query = $this->db->get("mqtt");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    //------------------------------------------------------------------------------------------------------
    function add_task($task)
    {
        if ($this->db->insert('site_tasks', $task)) {
            return true;
        } else {
            return false;
        }
    }
    //------------------------------------------------------------------------------------------------------
    function get_user_tasks($user_id)
    {
        $result = array("user_id" => $user_id, "date_given" => date('Y-m-d'));
        //$this->db->where($result);
        $this->db->where("user_id", $user_id);
        $this->db->select('*');
        $this->db->from('admin_task');
        $this->db->order_by('date_given');
        $query = $this->db->get();
        return $query->result();
    }
    //------------------------------------------------------------------------------------------------------

    function get_all_users_and_display_in_things_to_do()
    {
        $this->db->select('users.user_id, first_name, last_name');
        $this->db->from('users');
        $this->db->where("user_group", 1);
        $this->db->order_by('users.user_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //------------------------------------------------------------------------------------------------------

    function task_update($task_id, $update_tasks)
    {
        $this->db->where("task_id", $task_id); //colomn name and id variable
        $this->db->update("admin_task", $update_tasks); //table and array parameters
    }

    //------------------------------------------------------------------------------------------------------

    function delete_task($task_id)
    {
        $this->db->where('task_id', $task_id);
        $this->db->delete('admin_task');
    }

    //------------------------------------------------------------------------------------------------------

    function get_all_users_not_admin()
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where("user_group", 1);
        $query = $this->db->get();
        return $query->result();
    }

    //------------------------------------------------------------------------------------------------------

    function add_team($team)
    {
        if ($this->db->insert('team', $team)) {
            return true;
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function add_member($member)
    {
        if ($this->db->insert('team_member', $member)) {
            return true;
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function get_latest_team_id()
    {

        $query = $this->db->query("SELECT * FROM `team`  order by team_id DESC limit 1");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_user_team($id)
    {

        $query = $this->db->get_where('team', array(
            'team_id' => $id
        ));
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------

    function delete_team($team_id)
    {
        $this->db->where('team_id', $team_id);
        $this->db->delete('team');
    }

    function delete_team_member($team_id)
    {
        $this->db->where('member_team_id', $team_id);
        $this->db->delete('team_member');
    }

    //------------------------------------------------------------------------------------------------------

    function get_joined_leader_id_and_user_id()
    {
        $this->db->select('user_id, first_name, last_name, team_name,team.date_created, team_id, team_leader');
        $this->db->from('team');
        $this->db->join('users', 'team.team_leader = users.user_id', 'left');
        $this->db->group_by('team.team_leader');
        $this->db->order_by('team.date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    //------------------------------------------------------------------------------------------------------

    function checking_team()
    {
        $this->db->select('*');
        $this->db->from('team');
        $query = $this->db->get();
        return $query->result();
    }

    function checking_team_member()
    {
        $this->db->select('*');
        $this->db->from('team_member');
        $query = $this->db->get();
        return $query->result();
    }

    function get_available_user($avail_user)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_id', $avail_user);
        $query = $this->db->get();
        return $query->result();
    }




    function get_team_leader_and_name()
    {
        $this->db->select('*');
        $this->db->from('team');
        $this->db->group_by('team_id');
        $query = $this->db->get();
        return $query->result();
    }
    function get_team_leader_and_name_v2($id)
    {
        $this->db->select('*');
        $this->db->from('team');
        $this->db->where('team_leader', $id);
        $this->db->group_by('team_id');
        $query = $this->db->get();
        return $query->result();
    }
    // //------------------------------------------------------------------------------------------------------

    function get_all_members_by_team_id($team_id)
    {

        $this->db->select('*');
        $this->db->from('team_member');
        $this->db->join('users', 'team_member_user_id = users.user_id');
        //$this->db->join('team','team_member.member_team_id = users.user_id');
        $this->db->where('member_team_id', $team_id);
        $query = $this->db->get();
        return $query->result();
    }

    //------------------------------------------------------------------------------------------------------

    function addTeamTask($team_id, $add_tasks)
    {
        $this->db->where("team_id", $team_id);
        $this->db->insert("team_task", $add_tasks);
    }
    function get_team_tasks($team_id)
    {
        $this->db->where('team_id', $team_id);
        $q = $this->db->get("team_task");
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }
    function task_team_update($team_task_id, $update_team_tasks)
    {
        $this->db->where("team_task_id", $team_task_id); //colomn name and id variable
        $this->db->update("team_task", $update_team_tasks); //table and array parameters
    }
    // function delete_team_task($team_task_id)
    // {
    //     $this->db->where('team_task_id', $team_task_id);
    //     $this->db->delete('team_task');
    // }

    function delete_member($team_member_id)
    {
        $this->db->where('team_member_user_id', $team_member_id);
        $this->db->delete('team_member');
    }

    // function delete_task_created_for_deleted_team($team_id)
    // {
    //     $this->db->where('team_id', $team_id);
    //     $this->db->delete('team_task');
    // }


    function get_video()
    {
        // $q = $this->db->get("video");
        // if($q->num_rows()>0){
        //     return $q->result();
        // }else{
        //     return false;
        // }
        $this->db->select('*');
        $this->db->from('video');
        $this->db->join('users', 'video.user_id = users.user_id');
        $query = $this->db->get();
        return $query->result();
    }
    function user_single()
    {
        $this->db->where("user_group", 1);
        $query = $this->db->get("users");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function send_vid($vid)
    {
        if ($this->db->insert('video', $vid)) {
            return true;
        } else {
            return false;
        }
    }

    function delete_vid($vid_id)
    {
        $this->db->where('video_id', $vid_id);
        $this->db->delete('video');
    }
    //------------------------------------------------------------------------------------------------------
    function get_newly_added_member($count)
    {

        $this->db->select('*');
        $this->db->from('team_member');
        $this->db->order_by('team_member_id', 'DESC');
        $this->db->limit($count);
        $query = $this->db->get();
        return $query->result();
    }
    //------------------------------------------------------------------------------------------------------

    function get_member_by_user_id($user_id)
    {
        $this->db->where('user_id', $user_id);
        $q = $this->db->get("users");
        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }
    function resend_vid($id)
    {
        $this->db->where("video_id", $id); //colomn name and id variable
        $this->db->update("video", array("played" => 0, "request" => 0)); //table and array parameters
    }
    //------------------------------------------------------------------------------------------------------
    function get_all_leaders()
    {
        $this->db->select('*');
        $this->db->from('team');
        $query = $this->db->get();
        return $query->result();
    }
    //------------------------------------------------------------------------------------------------------
    function save_site_name($site_name)
    {
        if ($this->db->insert('sites', $site_name)) {
            return true;
        } else {
            return false;
        }
    }

    function save_site_properties_and_value($property_array)
    {
        if ($this->db->insert('property', $property_array)) {
            return true;
        } else {
            return false;
        }
    }

    function get_latest_site_id()
    {

        $this->db->select('*');
        $this->db->from('sites');
        $this->db->order_by('site_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();
    }
    //---------FOR TASK SITE----------

    function get_site_tasks($site_id)
    {
        $this->db->where("site_id", $site_id);
        $this->db->select('*');
        $this->db->from('site_tasks');
        $this->db->order_by('task_date');
        $query = $this->db->get();
        return $query->result();
    }

    function edit_task($task_id, $edit_task_array)
    {
        $this->db->where("site_task_id", $task_id);
        $this->db->update("site_tasks", $edit_task_array);
    }

    function delete_task_joined_with_assigned_task($task_id)
    {
        $this->db->where('site_task_id', $task_id);
        $this->db->delete('site_tasks');
    }

    function delete_assigned_task_joined_with_site_tasks($site_id)
    {
        $this->db->where('assigned_site_id', $site_id);
        $this->db->delete('assigned_task');
    }


    function delete_team_member_task_for_deleted_team_member($team_member_id)
    {
        $this->db->where('team_member_user_id', $team_member_id);
        $this->db->delete('team_member_task');
    }



    function get_all_sites()
    {
        $this->db->select('*');
        $this->db->from('sites');
        $query = $this->db->get();
        return $query->result();
    }

    function get_task_by_site_id($site_id)
    {
        $this->db->select('*');
        $this->db->from('site_tasks');
        $this->db->where_in('site_id', $site_id);
        $query = $this->db->get();
        return $query->result();
    }

    function save_assign_task($assigned)
    {
        if ($this->db->insert('assigned_task', $assigned)) {
            return true;
        } else {
            return false;
        }
    }

    function get_assigned_tasks_by_user_id()
    {
        $this->db->select('*');
        $this->db->from('assigned_task');
        $this->db->join('users', 'assigned_task.user_id = users.user_id', 'left');
        $this->db->join('team', 'assigned_task.team_id = team.team_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function cancel_assigned_site($site_id)
    {
        $this->db->where('assigned_site_id', $site_id);
        $this->db->delete('assigned_task');
    }

    function delete_assigned_task_for_the_team($team_id)
    {
        $this->db->where('team_id', $team_id);
        $this->db->delete('assigned_task');
    }

    function delete_assigned_task_for_the_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('assigned_task');
    }

    function get_all_online_employees()
    {
        $this->db->select('*');
        $this->db->from('login_details');
        $this->db->join('users', 'login_details.login_details_user_id = users.user_id', 'left');
        $this->db->group_by('login_details.login_details_user_id');
        $query = $this->db->get();
        return $query->result();
    }

    function force_logout_user($user_id)
    {
        $this->db->where('login_details_user_id', $user_id);
        $this->db->delete('login_details');
    }

    function force_logout_all_user()
    {
        $this->db->empty_table('login_details');
    }

    function delete_user_from_mqtt($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('mqtt');
    }

    function get_latlong()
    {
        $this->db->select('*');
        $this->db->from('mqtt');
        $this->db->join('users', 'mqtt.user_id = users.user_id');
        $this->db->join('login_details', 'mqtt.user_id = login_details.login_details_user_id');
        $this->db->group_by('mqtt.user_id');
        $query = $this->db->get();
        return $query->result();
    }

    function  get_evidence($task_id)
    {
        $this->db->select('*');
        $this->db->from('required');
        $this->db->where('task_id', $task_id);
        $this->db->group_by('file');
        $query = $this->db->get();
        return $query->result();
    }

    function update_task_status_to_completed($site_task_id, $site_tasks)
    {
        $this->db->where("site_task_id", $site_task_id);
        $this->db->update("site_tasks", $site_tasks);
    }

    function get_completed_task($site_task_id)
    {
        $this->db->select('*');
        $this->db->from('site_tasks');
        $this->db->where('site_task_id', $site_task_id);
        $query = $this->db->get();
        return $query->result();
    }

    function not_satisfied_evidence($site_task_id, $site_tasks)
    {
        $this->db->where("site_task_id", $site_task_id);
        $this->db->update("site_tasks", $site_tasks);
    }

    function delete_evidence_not_satisfied($task_id, $file_type)
    {
        $evidence = array("file_type" => $file_type, "task_id" => $task_id);
        $this->db->where($evidence);
        $this->db->delete('required');
    }

    function get_site_task_by_site_task_id_to_display_if_bad_evidence($site_task_id)
    {
        $this->db->select('*');
        $this->db->from('site_tasks');
        $this->db->where('site_task_id', $site_task_id);
        $query = $this->db->get();
        return $query->result();
    }

    function display_if_evidence_not_satisfied()
    {
        $this->db->select('*');
        $this->db->from('site_tasks');
        $query = $this->db->get();
        return $query->result();
    }

    function getUser($user_id)
    {
        $q = $this->db->get_where('users', array('user_id' => $user_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function getTeam($team_id)
    {
        $q = $this->db->get_where('team', array('team_id' => $team_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function addReport($report_data)
    {
        if ($this->db->insert('reports', $report_data)) {
            return true;
        } else {
            return false;
        }
    }

    function getReports($site_id)
    {
        // $this->db->select('*');
        // $this->db->from('reports');
        // $query = $this->db->get();
        // return $query->result();

        $query = $this->db->query("SELECT 
        reports.reports_id,
        reports.user_id,
        reports.team_id,
        reports.text_report,
        reports.report_date,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name,
        reports.* 
        FROM reports 
        
         LEFT JOIN users 
         ON reports.user_id = users.user_id 

         LEFT JOIN team 
         ON reports.team_id = team.team_id 

        where reports.site_id = '$site_id'
        ");
        return $query->result();
    }
    function  getReport($site_id)
    {
        $query = $this->db->query("SELECT 
        reports.reports_id,
        reports.user_id,
        reports.team_id,
        reports.text_report,
        reports.report_date,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name,
        reports.* 
        FROM reports 
         LEFT JOIN users 
         ON reports.user_id = users.user_id 

         LEFT JOIN team 
         ON reports.team_id = team.team_id 

        where reports.site_id = '$site_id'
        ");
        return $query->result();
    }

    function getSite($site_id)
    {
        $q = $this->db->get_where('sites', array('site_id' => $site_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }

    function  getReportsDate($site_id, $from, $to)
    {
        $this->db->select('reports.*,users.*,team.*,
        reports.reports_id,
        reports.user_id,
        reports.team_id,
        reports.text_report,
        reports.report_date,
        users.first_name,
        users.last_name,
        team.team_name,
        team.team_leader,
        team.team_leader_name');
        $this->db->from('reports');
        $this->db->where('reports.site_id', $site_id);
        $this->db->where('reports.report_date >=', $from);
        $this->db->where('reports.report_date <=', $to);
        $this->db->join('users', 'reports.user_id = users.user_id', 'left');
        $this->db->join('team', 'reports.team_id = team.team_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function get_latest_user()
    {
        return $this->db->insert_id();
    }

    function get_users_skills()
    {
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();
        return $query->result();
    }
    function add_sites($data)
    {
        if ($this->db->insert('sites', $data)) {
            return true;
        } else {
            return false;
        }
    }
    function update_sites($site_id, $update_data)
    {
        $this->db->where("site_id", $site_id);
        $this->db->update("sites", $update_data);
    }

    function addDisposal($data)
    {
        if ($this->db->insert('disposal', $data)) {
            return true;
        } else {
            return false;
        }
    }

    function getDisposal()
    {
        $this->db->select('*');
        $this->db->from('disposal');
        $query = $this->db->get();
        return $query->result();
    }

    function getDisposalToShow($disposal_id)
    {
        $this->db->select('*');
        $this->db->from('disposal');
        $this->db->where('disposal_id', $disposal_id);
        $query = $this->db->get();
        return $query->result();
    }

    function updateDisposal($disposal_id, $data)
    {
        $this->db->where("disposal_id", $disposal_id);
        $this->db->update("disposal", $data);
    }

    function deleteDisposal($disposal_id)
    {
        $this->db->where('disposal_id', $disposal_id);
        $this->db->delete('disposal');
    }

    function getDisposalForPdf($disposal_id)
    {
        $q = $this->db->get_where('disposal', array('disposal_id' => $disposal_id), 1);
        if ($q->num_rows() > 0) {
            $res = $q->result();
            return $res[0];
        } else {
            return false;
        }
    }
    function lastID()
    {
        return $this->db->insert_id();
    }
}
