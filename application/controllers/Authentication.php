<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Bangkok');

class Authentication extends CI_Controller
{

	public function index()
	{
		$this->load->library('session');
		$this->load->model("users");
		if (isset($_POST['email'])) {

			$email = $_POST['email'];
			$password = $_POST['password'];
			$user = array("email" => $email, "password" => $password);
			$res = $this->users->get_user($user);
			//$_SESSION['user'] = $res;
			//$_SESSION['user_arr'] = (array) $res;
			$this->session->set_userdata('user', (array) $res);
			if ($_SESSION['user']['user_group'] == 1) {
				echo "not admin";
				exit();
			}

			if ($res) {

				if ($_SESSION['user']) {
					echo "success";
				} else {
					echo "dont exist";
				}
			}
		}
	}





	public function register()
	{
		$this->load->model('users');

		if (isset($_POST['first_name'])) {
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$skill = $_POST['skill'];
			$date = date('Y-m-d');
			$user_group = 1;

			$comma_separated = implode(", ", $skill);

			$users = array(
				"first_name" => $first_name,
				"last_name" => $last_name,
				"address" => $address,
				"phone" => $phone,
				"email" => $email,
				"password" => $password,
				"skills" => $comma_separated,
				"date_created" => $date,
				"user_group" => $user_group
			);
			$this->users->add_user($users);
			$latest_user = $this->users->get_latest_user();
		} else {
			echo "do not recive";
		}
	} //=================register closing =================


	public function admin_users_view()
	{
		$this->load->library('session');
		$this->load->model("users");

		$this->load->view('telco_ui');
	} //=================admin_users_view closing =================

	public function json_users()
	{

		$this->load->library('session');
		$this->load->model("users");

		$all_users["data"] = $this->users->get_all_users();
		//$all_users = $this->users->get_all_users();
		echo json_encode($all_users);
	}

	public function json_videos()
	{

		$this->load->library('session');
		$this->load->model("video_model");

		$all_videos["data"] = $this->video_model->get_all_videos();
		//$all_videos = $this->users->get_all_videos();
		echo json_encode($all_videos);
	}

	public function add_video(){
		$this->load->library('session');
		$this->load->model("video_model");
		//move_uploaded_file($_FILES["file"]["tmp_name"], './videos/' . $_FILES["file"]["name"]);
		$data = $this->input->post();
		$data['video_path'] = $this->_upload_video($_FILES['video_path']);
		$data['uploader_id'] = @$this->session->userdata('user')['user_id'];
		$this->video_model->add_video($data);
	}

	function _upload_video($file = null)
	{
		$this->load->library('session');
		if (isset($file) && $file != null) {
			time();
			$extension = explode('.', $file['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './videos/' . $new_name;
			move_uploaded_file($file['tmp_name'], $destination);
			return 'videos/' . $new_name;
		} else {
			return '';
		}
	}
	public function delete_user()
	{
		$this->load->library('session');
		$this->load->model('users');
		if (isset($_POST['user_id'])) {
			$leaders = $this->users->get_all_leaders();
			foreach ($leaders as $l) {
				$taken_leaders[] = $l->team_leader;
			}
			if (in_array($_POST['user_id'], $taken_leaders, TRUE)) {
				echo "used";
			} else {
				$user_id = $_POST['user_id'];
				$this->users->delete_User($user_id);
				$this->users->delete_assigned_task_for_the_user($user_id);
				$this->users->delete_team_member_task_for_deleted_team_member($user_id);
				$this->users->delete_team_member($user_id);
				$this->users->force_logout_user($user_id);
				$this->users->delete_user_from_mqtt($user_id);
			}
		}
	}


	public function update_video()
	{
		$this->load->library('session');
		$this->load->model('video_model');
		$params['video_id'] = $_POST['video_id'];
		$params['video_title'] = $_POST['video_title'];
		$params['video_description'] = $_POST['video_description'];
		$params['confidential'] = $_POST['confidential'];
		$this->video_model->update_video($params);
		echo "Update";
	}


	public function update_user()
	{
		$this->load->library('session');
		$this->load->model('users');
		$user_id = $_POST['edit_user_id'];

		if (!isset($_POST['edit_skill'])) {
			echo 'no skills';
		} else {
			if (isset($_POST['edit_user_id'])) {

				$user_id = $_POST['edit_user_id'];
				$first_name = $_POST['edit_first_name'];
				$last_name = $_POST['edit_last_name'];
				$address = $_POST['edit_address'];
				$phone = $_POST['edit_phone'];
				$email = $_POST['edit_email'];
				$password = $_POST['edit_password'];
				$skills = $_POST['edit_skill'];

				$comma_separated = implode(", ", $skills);

				$update_data = array(
					"first_name" => $first_name,
					"last_name" => $last_name,
					"address" => $address,
					"phone" => $phone,
					"email" => $email,
					"password" => $password,
					"skills" => $comma_separated
				);
				$this->users->update_user($user_id, $update_data);
				echo "Update";
			}
		}
	}

	public function user_location()
	{
		$this->load->model('users');
		if (isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
			$data['location'] = $this->users->get_user_location($user_id);
			echo json_encode($data);
		}
	}



	public function users_task()
	{
		$this->load->model('users');
		$user_task = $this->users->get_user_tasks($_POST['user_id']);
		echo json_encode($user_task);
	}

	public function update_task()
	{
		$this->load->model('users');
		$update_tasks = array(
			"task_id" => $_POST['task_id'],
			"task_today" => $_POST['task'],
			"date_given" => $_POST['date_given_task'],
			"required_video_evidence" => $_POST['required_video_evidence'],
			"required_image_evidence" => $_POST['required_image_evidence']
		);
		$this->users->task_update($_POST['task_id'], $update_tasks);
	}

	public function delete_task()
	{
		$this->load->model('users');
		if (isset($_POST['task_id'])) {
			$this->users->delete_task($_POST['task_id']);
			//echo "success";
		}
	}


	public function table_task()
	{
		$this->load->model('users');
		$joined_users_and_task["data"] = $this->users->get_all_users_and_display_in_things_to_do();
		echo json_encode($joined_users_and_task);
	}


	public function select_leader()
	{
		$this->load->model('users');
		//if(isset($_POST['request_users'])){
		$all_user = $this->users->get_all_users_not_admin();
		$taken_user = $this->users->checking_team();
		$taken_team_member = $this->users->checking_team_member();

		$all = array();
		$taken = array();
		$mt = array();

		foreach ($all_user as $as) {
			$all[] = $as->user_id;
		}

		if (!$taken_user) {
			$taken = array();
		} else {
			foreach ($taken_user as $tu) {
				$taken[] = $tu->team_leader;
			}
		}

		if (!$taken_team_member) {
			$mt = array();
		} else {
			foreach ($taken_team_member as $atm) {
				$mt[] = $atm->team_member_user_id;
			}
		}


		$first_filter = array_diff($all, $taken);
		$filtered = array_diff($first_filter, $mt);
		$t = array();
		if (!$filtered) {
			echo json_encode($t);
		} else {
			foreach ($filtered as $f => $u) {
				$new = $this->users->get_available_user($u);
				foreach ($new as $n)
					$t[] = array("user_id" => $n->user_id, "first_name" => $n->first_name, "last_name" => $n->last_name);
			}
			echo json_encode($t);
		}
	}

	public function save_team()
	{
		$this->load->model('users');
		if (isset($_POST['team_name'])) {
			$team = array(
				"team_name" => $_POST['team_name'],
				"team_leader" => $_POST['team_leader'],
				"team_leader_name" => $_POST['passed_attr'],
				"date_created" => date('Y-m-d'),
			);
			$this->users->add_team($team);
		}
	}

	public function get_latest_team_id()
	{
		$this->load->model('users');
		if (isset($_POST['request_users'])) {
			$data = $this->users->get_latest_team_id();
			echo  json_encode($data);
		}
	}

	public function get_user_team()
	{
		$this->load->model('users');
		if (isset($_POST['user_id'])) {
			$data = $this->users->get_user_team($_POST['user_id']);
			echo  json_encode($data);
		}
	}
	public function save_member()
	{
		$this->load->model('users');
		$member_name = $_POST["name"];
		$team_id = $_POST['latest_team_id'];
		//echo $team_id;
		//print_r($member_name);
		// if(count(array_unique($member_name))<count($member_name)){s
		// 	// Array has duplicates
		// }else{
		// 	// Array does not have duplicates
		// }

		$filtered_array = array_unique($member_name);
		foreach ($filtered_array as $key => $value) {
			$members = array(
				"member_team_id" => $team_id,
				"team_member_user_id" => $value,
			);
			$this->users->add_member($members);
		}
	}

	public function delete_closed_team()
	{

		$this->load->model('users');
		if (isset($_POST['latest_team_id'])) {
			$this->load->model('users');
			$this->users->delete_team($_POST['latest_team_id']);
			echo "success";
		}
	}

	public function get_team_leader_and_name_v2()
	{
		$this->load->model('users');
		//if(isset($_POST['request_team'])){
		$get_all_team = $this->users->get_team_leader_and_name_v2($_POST['user_id']);
		$data = '';
		$current_team_id = '';
		foreach ($get_all_team as $team) {
			$data .= '<div class="grid-team" data-delete_team_id="' . $team->team_id . '"">
								<ul id="appended_team" class="list-group append_new' . $team->team_id . '" >';
			$data .= '		
									<li class="list-group-item list-group-item-info"><label>Team Name: </label>&nbsp;&nbsp;<label>' . $team->team_name . '</label></li>
									<li class="list-group-item"><label>Leader: </label>&nbsp;&nbsp;' . $team->team_leader_name . '</li>&nbsp&nbsp;&nbsp&nbsp;<label>Members</label>
									<center><p style="font-weight:bold;"><a id="add_more_member" data-toggle="modal" data-target="#add_more_member_modal" data-team_id="' . $team->team_id . '">Add more member</a></p></center>';

			$get_member = $this->users->get_all_members_by_team_id($team->team_id);
			foreach ($get_member as $m) {
				$data .= '<li class="list-group-item m" data-member_user_id="' . $m->team_member_user_id . '">&nbsp;&nbsp; ' . $m->first_name . ' ' . $m->last_name . '<a id="delete_solo_member" team_member_id="' . $m->team_member_user_id . '"><span class="glyphicon glyphicon-trash"></span></a></li>';
			}
			$data .= 	'</ul>
						   </div>';
		}
		echo $data;
		//}
	}

	public function get_team_leader_and_name()
	{
		$this->load->model('users');
		//if(isset($_POST['request_team'])){
		$get_all_team = $this->users->get_team_leader_and_name();
		$data = '';
		$current_team_id = '';
		foreach ($get_all_team as $team) {
			$data .= '<div class="grid-team" data-delete_team_id="' . $team->team_id . '"">
								<ul id="appended_team" class="list-group append_new' . $team->team_id . '" >';
			$data .= '		
									<a class="delete_team_and_members" team_id="' . $team->team_id . '">&times;</a>
									<li class="list-group-item list-group-item-info"><label>Team Name: </label>&nbsp;&nbsp;<label>' . $team->team_name . '</label></li>
									<li class="list-group-item"><label>Leader: </label>&nbsp;&nbsp;' . $team->team_leader_name . '</li>&nbsp&nbsp;&nbsp&nbsp;<label>Members</label>
									<center><p style="font-weight:bold;"><a id="add_more_member" data-toggle="modal" data-target="#add_more_member_modal" data-team_id="' . $team->team_id . '">Add more member</a></p></center>';

			$get_member = $this->users->get_all_members_by_team_id($team->team_id);
			foreach ($get_member as $m) {
				$data .= '<li class="list-group-item m" data-member_user_id="' . $m->team_member_user_id . '">&nbsp;&nbsp; ' . $m->first_name . ' ' . $m->last_name . '<a id="delete_solo_member" team_member_id="' . $m->team_member_user_id . '"><span class="glyphicon glyphicon-trash"></span></a></li>';
			}
			$data .= 	'</ul>
						   </div>';
		}
		echo $data;
		//}
	}

	public function add_team_task()
	{
		$this->load->model('users');
		$team_id = $_POST['team_id'];
		$add_tasks = array(
			"team_id" => $_POST['team_id'],
			"task" => $_POST['task'],
			"date_given" => $_POST['team_date_given_task'],
			"required_video_evidence" => $_POST['require_video_evidence'],
			"required_image_evidence" => $_POST['require_image_evidence'],
			"task_status" => 0
		);
		$this->users->addTeamTask($team_id, $add_tasks);
	}

	public function team_task()
	{
		$this->load->model('users');
		$team_id = $_POST['team_id'];
		$team_task = $this->users->get_team_tasks($team_id);
		echo json_encode($team_task);
	}

	public function update_team_task()
	{
		$this->load->model('users');
		$team_task_id = $_POST['team_task_id'];
		$update_team_tasks = array(
			"team_task_id" => $_POST['team_task_id'],
			"task" => $_POST['task'],
			"date_given" => $_POST['edit_team_date_given_task'],
			"required_video_evidence" => $_POST['require_video_evidence'],
			"required_image_evidence" => $_POST['require_image_evidence']
		);
		$this->users->task_team_update($team_task_id, $update_team_tasks);
	}

	public function delete_team_task()
	{
		$this->load->model('users');
		if (isset($_POST['team_task_id'])) {
			$this->load->model('users');
			$team_task_id = $_POST['team_task_id'];
			$this->users->delete_team_task($team_task_id);
			//echo "success";
		}
	}

	public function delete_team()
	{
		$this->load->model('users');
		if (isset($_POST['team_id'])) {
			$this->users->delete_team($_POST['team_id']);
			$this->users->delete_team_member($_POST['team_id']);
			//$this->users->delete_task_created_for_deleted_team($_POST['team_id']);
			$this->users->delete_assigned_task_for_the_team($_POST['team_id']);
		}
	}


	public function append_after_adding_team()
	{
		$this->load->model('users');
		if (isset($_POST['request_latest_team'])) {
			$team = '';
			$data = $this->users->get_latest_team_id();
			foreach ($data as $d) {
				$get_member = $this->users->get_all_members_by_team_id($d->team_id);
				$team .= '<div class="grid-team" data-delete_team_id="' . $d->team_id . '">	
					<ul id="appended_team" class="list-group append_new' . $d->team_id . '">';
				$team .= '		
					<a class="delete_team_and_members" team_id="' . $d->team_id . '">&times;</a>
					<li class="list-group-item list-group-item-info"><label>Team Name: </label>&nbsp;&nbsp;<label>' . $d->team_name . '</label></li>
					<li class="list-group-item"><label>Leader: </label>&nbsp;&nbsp;' . $d->team_leader_name . '</li>&nbsp&nbsp;&nbsp&nbsp;<label>Members</label>
					<center><p style="font-weight:bold;"><a id="add_more_member" data-toggle="modal" data-target="#add_more_member_modal" data-team_id="' . $d->team_id . '">Add more member</a></p></center>';

				foreach ($get_member as $m) {
					$team .= '<li class="list-group-item m" data-member_user_id="' . $m->team_member_user_id . '">&nbsp;&nbsp; ' . $m->first_name . ' ' . $m->last_name . '<a id="delete_solo_member" team_member_id="' . $m->team_member_user_id . '"><span class="glyphicon glyphicon-trash"></span></a></li>';
				}
				$team .= 	'</ul>
			</div>';
			}
			echo $team;
		}
	}

	public function delete_team_member()
	{
		$this->load->model('users');
		if (isset($_POST['team_member_id'])) {
			$this->users->delete_member($_POST['team_member_id']);
			$this->users->delete_team_member_task_for_deleted_team_member($_POST['team_member_id']);
		}
	}


	public function single_user()
	{
		$this->load->model("users");
		$single_user = $this->users->user_single();
		echo json_encode($single_user);
	}

	public function upload_video()
	{
		move_uploaded_file($_FILES["file"]["tmp_name"], './uploads/' . $_FILES["file"]["name"]);
	}

	public function save_video()
	{
		$this->load->model("users");

		$video = array("video_name" => $_POST['video_name'], "video" => $_POST['file_name'], "user_id" => $_POST['users'], "played" => 0, "task_id" => $_POST['site_task_id']);
		$res = $this->users->send_vid($video);
	}
	public function save_image()
	{
		$this->load->model("users");

		$video = array("image_name" => $_POST['image_name'], "image" => $_POST['file_name'], "user_id" => $_POST['users'], "seen" => 0);
		$res = $this->users->send_vid($video);
	}
	public function resend_video()
	{
		$this->load->model("users");
		$this->users->resend_vid($_POST['video_id']);
	}
	public function sent_video()
	{
		$this->load->model('users');
		$video["data"] = $this->users->get_video();
		echo json_encode($video);
	}
	public function delete_video()
	{
		$this->load->model('users');
		$this->users->delete_vid($_POST['video_id']);
		echo "deleted";
	}
	public function delete_video_v2()
	{
		$this->load->model('video_model');
		$this->video_model->delete_vid($_POST['video_id']);
		echo "deleted";
	}

	public function send_choose()
	{
		$this->load->model('users');
		$video = $this->_upload_choose();
		$title = $_POST['video_name'];
		$user = $_POST['users'];
		$task_id = $_POST['site_task_id'];
		$data = array("video_name" => $title, "video" => $video, "user_id" => $user, "played" => 0, "task_id" => $task_id);
		$res = $this->users->send_vid($data);
	}



	function _upload_choose()
	{

		$this->load->library('session');
		if (isset($_FILES['file_name'])) {
			time();
			$extension = explode('.', $_FILES['file_name']['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './uploads/' . $new_name;
			move_uploaded_file($_FILES['file_name']['tmp_name'], $destination);
			return $new_name;
		}
	}


	public function save_more_member()
	{
		$this->load->model('users');
		$member_name = $_POST["add_name"];
		$team_id = $_POST['clicked_team_id'];


		$filtered_array = array_unique($member_name);
		foreach ($filtered_array as $key => $value) {
			$members = array(
				"member_team_id" => $team_id,
				"team_member_user_id" => $value,
			);
			$this->users->add_member($members);
		}
		echo count($filtered_array);
	}

	public function get_new_member()
	{
		$this->load->model('users');
		if (isset($_POST['limit'])) {
			$count = $_POST['limit'];
			$new_member = $this->users->get_newly_added_member($count);
			//$return_newly_added_member = array();
			foreach ($new_member as $key) {
				$key->team_member_user_id;
				$newly_added_members = $this->users->get_member_by_user_id($key->team_member_user_id);
				foreach ($newly_added_members as $n)
					$return_newly_added_member[] = array("user_id" => $n->user_id, "first_name" => $n->first_name, "last_name" => $n->last_name);
			}
			echo json_encode($return_newly_added_member);
		}
	}

	public function save_site_name()
	{
		$this->load->model('users');
		if (isset($_POST['site_name'])) {
			$site = array(
				"site_name" => $_POST['site_name'],
				"site_date_created" => date('Y-m-d')
			);
			$this->users->save_site_name($site);
		}
	}



	public function get_sites()
	{
		$this->load->model('site_model');
		$sites['data'] = $this->site_model->get_sites();
		echo json_encode($sites);
	}


	public function edit_site_name_and_properties()
	{
		$this->load->model('site_model');
		$this->load->model('users');
		if (isset($_POST['edit_site_id'])) {

			$site_id = $_POST['edit_site_id'];
			$site_name = $_POST['edit_site_name'];

			$site_property = $_POST['edit_property_name'];
			$site_value = $_POST['edit_property_value'];
			$sort_id = $_POST['edit_sort_id'];



			$site_name = array(
				"site_name" => $site_name
			);
			$this->site_model->update_site_name($site_id, $site_name);

			$this->site_model->delete_site_properties($site_id);

			for ($i = 0; $i < count($site_property); $i++) {
				$update_site_properties = array(
					"property_site_id" => $site_id,
					"property_name" => $site_property[$i],
					"property_value" => $site_value[$i],
					"sort_order" => $sort_id[$i]
				);
				$this->users->save_site_properties_and_value($update_site_properties);
			}
		}
	}


	//===================for site property=====================================================================================================================

	// public function save_site_and_details()
	// {
	// 	$this->load->model('site_model');
	// 	if (isset($_POST['add_site_name'])) {

	// 		);
	// 		$this->site_model->saveSite($site);
	// 		echo 'save succeded!';
	// 	}
	// }

	public function display_site_name_in_table()
	{
		$this->load->model('site_model');
		$site['data'] = $this->site_model->get_site_and_site_detailes();
		echo json_encode($site);
	}

	public function get_site_detailes_by_id()
	{
		$this->load->model('site_model');
		$site_detailes = $this->site_model->get_site_detailes_by_id($_POST['site_id']);
		echo json_encode($site_detailes);
	}

	//display delete ========================================================================================================//
	public function delete_selected_sites()
	{
		if (isset($_POST['site_id'])) {
			$this->load->model('site_model');
			$this->site_model->delete_sites($_POST['site_id']);
			$this->site_model->delete_site_assigned_site($_POST['site_id']);
			$this->site_model->delete_site_tasks_if_the_site_has_been_deleted($_POST['site_id']);
			$this->site_model->delete_team_member_task($_POST['site_id']);
		}
	}
	//update sites ========================================================================================================//
	public function update_selected_sites()
	{
		$this->load->model('site_model');
		$site_id = $_POST['edit_site_id'];
		if (isset($_POST['edit_site_id'])) {
			$site = array(
				"site_name" => $_POST['edit_site_name'],
				"region" => $_POST['edit_region'],
				"market_cluster" => $_POST['edit_market_cluster'],
				"market" => $_POST['edit_market'],
				"u_sid" => $_POST['edit_u_sid'],
				"fa_location_code" => $_POST['edit_fa_location_code'],
				"gsm_site_id" => $_POST['edit_gsm_site_id'],
				"umts_site_id" => $_POST['edit_umts_site_id'],
				"lte_site_id" => $_POST['edit_lte_site_id'],
				"location_name" => $_POST['edit_location_name'],
				"street_address" => $_POST['edit_street_address'],
				"city" => $_POST['edit_city'],
				"state" => $_POST['edit_state'],
				"country" => $_POST['edit_country'],
				"zip" => $_POST['edit_zip'],
				"latitude_dec" => $_POST['edit_latitude_dec'],
				"longitude_dec" => $_POST['edit_longitude_dec'],
				"ops_district" => $_POST['edit_ops_district'],
				"ops_zone" => $_POST['edit_ops_zone'],
				"structure_type" => $_POST['edit_structure_type'],
				"site_directions" => $_POST['edit_site_directions'],
				"site_parking" => $_POST['edit_site_parking'],
				"access_detailes" =>  $_POST['edit_access_detailes'],
				"monday_hours" => $_POST['edit_monday_hours'],
				"tuesday_hours" => $_POST['edit_tuesday_hours'],
				"wednesday_hours" => $_POST['edit_wednesday_hours'],
				"thursday_hours" => $_POST['edit_thursday_hours'],
				"friday_hours" => $_POST['edit_friday_hours'],
				"saturday_hours" => $_POST['edit_saturday_hours'],
				"sunday_hours" => $_POST['edit_sunday_hours'],
				"access_list" => $_POST['edit_access_list'],
				"keys_combo" => $_POST['edit_keys_combo'],
				"key_comments" => $_POST['edit_key_comments'],
				"notice_needed" => $_POST['edit_notice_needed'],
				"notice_comments" => $_POST['edit_notice_comments'],
				"ladder_lift_req" => $_POST['edit_ladder_lift_req'],
				"ladder_height" => $_POST['edit_ladder_height'],
				"ladder_lift_note" => $_POST['edit_ladder_lift_note'],
				"site_hazard_comment" => $_POST['edit_site_hazard_comment'],
				"special_contract_restrictions" => $_POST['edit_special_contract_restrictions'],
				"traccess_serial_number" => $_POST['edit_traccess_serial_number'],
				"traccess_location" => $_POST['edit_traccess_location'],
				"primary_tech" => $_POST['edit_primary_tech'],
				"cell_phone" => $_POST['edit_cell_phone'],
				"on_call_tech" => $_POST['edit_on_call_tech'],
				"manager" => $_POST['edit_manager']
			);
			$this->site_model->update_sites($site_id, $site);
		}
	}
	// for sites data  editing=============================================================================//
	public function get_detailes_of_selected_sites()
	{
		// $site_id = $this->input->post('site_id');
		$this->load->model('site_model');
		$site_detailes = $this->site_model->get_sites_details($_POST['site_id']);
		echo json_encode($site_detailes);
	}

	//------------THIS IS FOR ADDING SITE TASK--------------------------------------------------------=============================

	public function save_site_task()
	{
		$this->load->model('users');
		if (isset($_POST['site_id'])) {
			$site_id = $_POST['site_id'];
			$site_task = $_POST['site_task'];





			//===================================================================================================================
			$skills = $_POST['skills_required'];
			$array_of_skills_required = explode(', ', $skills); //required skills

			$array_of_users_skill = $this->users->get_users_skills(); //skills of users
			$user_id_qualified = '';

			foreach ($array_of_users_skill as $users_skill) {
				$users_skill = $users_skill->skills . ', ' . $users_skill->user_id; //skills of users
				$users_skill_converted_to_array = explode(', ', $users_skill);
				if (count(array_intersect($array_of_skills_required, $users_skill_converted_to_array)) == count($array_of_skills_required)) {
					$qualified_users = $users_skill_converted_to_array;
					$user_id_qualified .=  end($qualified_users) . ', ';
				}
			}
			$user_id_qualified_to_array = explode(', ', $user_id_qualified);

			if (($key = array_search('', $user_id_qualified_to_array)) !== false) {
				unset($user_id_qualified_to_array[$key]);
			}

			if ($user_id_qualified_to_array) {
				$pick_random_user = array_rand($user_id_qualified_to_array, 1);
				$random_user = $user_id_qualified_to_array[$pick_random_user];
				$assign_user_to_a_task_upon_creating = array(
					"user_id" => $random_user,
					"assigned_site_id" => $site_id
				);

				$this->users->save_assign_task($assign_user_to_a_task_upon_creating);


				$task = array(
					"site_id" => $site_id,
					"site_task" => $site_task,
					"task_date" => $_POST['site_date_given_task'],
					"skills_required" => $_POST['skills_required'],
					"required_video_evidence" => $_POST['required_video_evidence'],
					"required_image_evidence" => $_POST['required_image_evidence'],
					"required_document_evidence" => $_POST['required_document_evidence'],
					"task_status" => 0
				);
				$this->users->add_task($task);
			} else {
				echo 'not_match';
			}
			//==========================================================================================================================
		}
	}

	public function call_saved_site_tasks()
	{
		$this->load->model('users');
		$site_tasks = $this->users->get_site_tasks($_POST['site_id']);
		echo json_encode($site_tasks);
	}

	public function delete_task_joined_with_assigned_task()
	{
		$this->load->model('users');
		$this->users->delete_task_joined_with_assigned_task($_POST['site_task_id']);
		$this->users->delete_assigned_task_joined_with_site_tasks($_POST['site_id']);
	}

	public function update_site_tasks()
	{
		$this->load->model('users');
		//echo 'edit_site_task =' . $_POST['edit_site_task'];
		$edit_task_array = array(
			"site_task" => $_POST['edit_site_task'],
			"task_date" => $_POST['edit_site_date_given_task'],
			"required_video_evidence" => $_POST['edit_required_video_evidence'],
			"required_image_evidence" => $_POST['edit_required_image_evidence'],
			"required_document_evidence" => $_POST['edit_required_document_evidence']
		);
		$this->users->edit_task($_POST['edit_task_id'], $edit_task_array);
	}

	public function show_all_site()
	{
		$this->load->model('users');
		$sites = $this->users->get_all_sites();
		echo json_encode($sites);
	}

	public function get_task_by_site_id()
	{
		$this->load->model('users');
		$site_task = $this->users->get_task_by_site_id($_POST['site_id']);
		if (!$site_task) {
			echo "null";
		} else {
			echo json_encode($site_task);
		}
	}

	public function save_assigned_task()
	{
		$this->load->model('users');
		$save_assign_task = array(
			"user_id" => $_POST['user_id'],
			"assigned_site_id" => $_POST['site_id']
		);
		$this->users->save_assign_task($save_assign_task);
	}



	public function save_assigned_team_task()
	{ //team assigning site
		$this->load->model('users');
		$save_assign_task = array(
			"team_id" => $_POST['team_id'],
			"assigned_site_id" => $_POST['site_id']
		);
		$this->users->save_assign_task($save_assign_task);
		//echo "succeded";
	}


	public function get_assigned_tasks()
	{
		$this->load->model('users');
		$assigned = $this->users->get_assigned_tasks_by_user_id();
		echo json_encode($assigned);
	}

	public function cancel_assigned_site()
	{
		$this->load->model('users');
		$this->users->cancel_assigned_site($_POST['site_id']);
		// echo "succeded!";
	}

	public function site_exported_detailes()
	{
		$site_id = $this->input->post('site_id');
		$this->load->model('site_model');
		$site_details = $this->site_model->get_site_to_export($site_id);

		$site_info = "";
		if ($site_details) {
			foreach ($site_details as $site) {
				if ($site->site_name != "dynamic") {
					$site_info .=
						'<tr>
						<td>' . $site->site_name . '</td>
						<td>' . $site->site_id . '</td>
                        <td>' . $site->region . '</td>
                        <td>' . $site->market_cluster . '</td>
						<td>' . $site->market . '</td>
						<td>' . $site->u_sid . '</td>
                        <td>' . $site->fa_location_code . '</td>
                        <td>' . $site->gsm_site_id . '</td>
						<td>' . $site->umts_site_id . '</td>
						<td>' . $site->lte_site_id . '</td>
                        <td>' . $site->location_name . '</td>
                        <td>' . $site->street_address . '</td>
						<td>' . $site->city . '</td>
						<td>' . $site->state . '</td>
                        <td>' . $site->country . '</td>
                        <td>' . $site->zip . '</td>
						<td>' . $site->latitude_dec . '</td>
						<td>' . $site->longitude_dec . '</td>
						<td>' . $site->ops_district . '</td>
						<td>' . $site->ops_zone . '</td>
                        <td>' . $site->structure_type . '</td>
                        <td>' . $site->site_directions . '</td>
						<td>' . $site->site_parking . '</td>
						<td>' . $site->access_detailes . '</td>
						<td>' . $site->monday_hours . '</td>
						<td>' . $site->tuesday_hours . '</td>
						<td>' . $site->wednesday_hours . '</td>
                        <td>' . $site->thursday_hours . '</td>
                        <td>' . $site->friday_hours . '</td>
						<td>' . $site->saturday_hours . '</td>
						<td>' . $site->sunday_hours . '</td>
                        <td>' . $site->access_list . '</td>
						<td>' . $site->keys_combo . '</td>
						<td>' . $site->key_comments . '</td>
						<td>' . $site->notice_needed . '</td>
						<td>' . $site->notice_comments . '</td>
						<td>' . $site->ladder_lift_req . '</td>
                        <td>' . $site->ladder_height . '</td>
                        <td>' . $site->ladder_lift_note . '</td>
						<td>' . $site->site_hazard_comment . '</td>
						<td>' . $site->special_contract_restrictions . '</td>
						<td>' . $site->traccess_serial_number . '</td>
						<td>' . $site->traccess_location . '</td>
						<td>' . $site->primary_tech . '</td>
                        <td>' . $site->cell_phone . '</td>
                        <td>' . $site->on_call_tech . '</td>
                        <td>' . $site->manager . '</td>
                    </tr>';
				} else {
					$site_info .=
						'<tr>
					<td>' . $site->site_name . '</td>
					<td>' . $site->site_id . '</td>
					<td>' . $site->region . '</td>
					<td>' . $site->market_cluster . '</td>
					<td>' . $site->market . '</td>
					<td>' . $site->u_sid . '</td>
					<td>' . $site->fa_location_code . '</td>
					<td>' . $site->gsm_site_id . '</td>
					<td>' . $site->umts_site_id . '</td>
					<td>' . $site->lte_site_id . '</td>
					<td>' . $site->location_name . '</td>
					<td>' . $site->street_address . '</td>
					<td>' . $site->city . '</td>
					<td>' . $site->state . '</td>
					<td>' . $site->country . '</td>
					<td>' . $site->zip . '</td>
					<td>' . $site->latitude_dec . '</td>
					<td>' . $site->longitude_dec . '</td>
					<td>' . $site->ops_district . '</td>
					<td>' . $site->ops_zone . '</td>
					<td>' . $site->structure_type . '</td>
					<td>' . $site->site_directions . '</td>
					<td>' . $site->site_parking . '</td>
					<td>' . $site->access_detailes . '</td>
					<td>' . $site->monday_hours . '</td>
					<td>' . $site->tuesday_hours . '</td>
					<td>' . $site->wednesday_hours . '</td>
					<td>' . $site->thursday_hours . '</td>
					<td>' . $site->friday_hours . '</td>
					<td>' . $site->saturday_hours . '</td>
					<td>' . $site->sunday_hours . '</td>
					<td>' . $site->access_list . '</td>
					<td>' . $site->keys_combo . '</td>
					<td>' . $site->key_comments . '</td>
					<td>' . $site->notice_needed . '</td>
					<td>' . $site->notice_comments . '</td>
					<td>' . $site->ladder_lift_req . '</td>
					<td>' . $site->ladder_height . '</td>
					<td>' . $site->ladder_lift_note . '</td>
					<td>' . $site->site_hazard_comment . '</td>
					<td>' . $site->special_contract_restrictions . '</td>
					<td>' . $site->traccess_serial_number . '</td>
					<td>' . $site->traccess_location . '</td>
					<td>' . $site->primary_tech . '</td>
					<td>' . $site->cell_phone . '</td>
					<td>' . $site->on_call_tech . '</td>
					<td>' . $site->manager . '</td>
                    </tr>

                ';
				}
			}
		}

		echo $site_info;
	}

	public function get_all_online_employees()
	{
		$this->load->library('session');
		$this->load->model("users");
		$users = $this->users->get_all_online_employees();
		$online['data'] = array_unique($users, SORT_REGULAR);
		echo json_encode($online);
	}

	public function logout_login_user()
	{
		$this->load->library('session');
		$this->load->model("users");
		$this->users->force_logout_user($_POST['user_id']);
		echo "delete successful!";
	}

	public function logout_logout_all_users()
	{
		$this->load->library('session');
		$this->load->model("users");
		$this->users->force_logout_all_user();
	}

	public function get_user_location()
	{
		$this->load->model('users');
		$this->load->library('session');
		$location = $this->users->get_latlong();
		echo json_encode($location);
		//print_r($location);
	}

	public function get_evidence()
	{
		$this->load->library('session');
		$this->load->model("users");
		$task_evidence = $this->users->get_evidence($_POST['site_task_id']);
		echo json_encode($task_evidence);
	}


	public function get_completed_task()
	{
		$this->load->library('session');
		$this->load->model("users");
		$task_status = $this->users->get_completed_task($_POST['site_task_id']);
		echo json_encode($task_status);
	}

	public function not_satisfied()
	{
		$this->load->library('session');
		$this->load->model("users");

		if (isset($_POST['file_type'])) {

			if ($_POST['file_type'] == 'video') {

				$site_tasks = array(
					"send_video" => 0,
					"video_alert" => 1,
				);
			} else if ($_POST['file_type'] == 'image') {

				$site_tasks = array(
					"send_image" => 0,
					"image_alert" => 1,
				);
			} else {
				$site_tasks = array(
					"send_document" => 0,
					"document_alert" => 1,
				);
			}
		}

		$this->users->not_satisfied_evidence($_POST['task_id'], $site_tasks);
		$this->users->delete_evidence_not_satisfied($_POST['task_id'], $_POST['file_type']);
		echo 'bad evidence';
	}

	public function get_site_task_by_site_task_id_to_display_if_bad_evidence()
	{
		$this->load->library('session');
		$this->load->model("users");
		$evidence_status = $this->users->get_site_task_by_site_task_id_to_display_if_bad_evidence($_POST['task_id']); //
		echo json_encode($evidence_status);
	}

	public function check_if_evidence_not_satisfied()
	{
		$this->load->library('session');
		$this->load->model("users");
		$if_evidence_not_satisfied = $this->users->display_if_evidence_not_satisfied();
		echo json_encode($if_evidence_not_satisfied);
	}


	public function json_report()
	{
		$this->load->library('session');
		$this->load->model("users");
		if (isset($_POST['site_id'])) {
			$site_id = $_POST['site_id'];
			if (isset($_POST['from'])) {
				$from = $_POST['from'];
				$to = $_POST['to'];
				$data = $this->users->getReportsDate($site_id, $from, $to);
				echo json_encode($data);
				// echo $site_id;
			} else {
				$reports = $this->users->getReports($site_id);
				echo json_encode($reports);
			}
		}
	}

	public function print_pdf($site_id = null, $from = null, $to = null)
	{
		$this->load->library('session');
		$this->load->model("users");
		if ($from == "" && $to == "") {
			$reports = $this->users->getReport($site_id);
		} else {
			$reports = $this->users->getReportsDate($site_id, $from, $to);
		}
		$site = $this->users->getSite($site_id);
		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Site Reports');
		$pdf->SetHeaderMargin(20);
		$pdf->SetTopMargin(10);
		$pdf->setFooterMargin(10);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->AddPage();
		$file = '';
		$html = "";
		$html .= '<table><tbody>';
		if (!$reports) { } else {
			$html .= "<br>";
			$html .= "<h4>Site Reports for " . $site->site_name . "</h4>";
			$html .= "<br>";
			foreach ($reports as $report) {
				$html .= '<br><tr>';
				if ($report->user_id != 0) {
					$html .= '<td>From: ' . $report->first_name . " " . $report->last_name . '</td>';
				} else {
					$html .= '<td>From: ' . $report->team_name . '</td>';
				}
				$html .= '<td>' . $report->text_report . '</td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= '<td>' . $report->report_date . '</td>';
				$html .= '</tr>';
				$file = $report->first_name . $report->site_id . 'pdf';
			}
		}
		$html .= '</tbody>
					</table>';
		$pdf->writeHTML($html, true, false, true, false, '');
		ob_end_clean();
		$pdf->Output('Reports.pdf', 'I');
		$pdf->Output('./uploads/reports.pdf', 'F');
		//echo '/uploads/reports.pdf';
	}

	public function email_reports($site_id = null, $from = null, $to = null)
	{
		$this->load->library('session');
		$this->load->model("users");
		if ($from == "" && $to == "") {
			$reports = $this->users->getReport($site_id);
		} else {
			$reports = $this->users->getReportsDate($site_id, $from, $to);
		}
		$site = $this->users->getSite($site_id);
		$to = $_POST['email'];
		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Site Reports');
		$pdf->SetHeaderMargin(20);
		$pdf->SetTopMargin(10);
		$pdf->setFooterMargin(10);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->AddPage();
		$html = "";
		$html .= '<table><tbody>';
		if (!$reports) { } else {
			$html .= "<br>";
			$html .= "<h4>Site Reports for " . $site->site_name . "</h4>";
			$html .= "<br>";
			foreach ($reports as $report) {
				$html .= '<br><tr>';
				if ($report->user_id != 0) {
					$html .= '<td>From: ' . $report->first_name . " " . $report->last_name . '</td>';
				} else {
					$html .= '<td>From: ' . $report->team_name . '</td>';
				}
				$html .= '<td>' . $report->text_report . '</td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$html .= '<td>' . $report->report_date . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</tbody>
					</table>';
		$pdf->writeHTML($html, true, false, true, false, '');
		ob_end_clean();
		$filename = "Attachment" . $site_id . ".pdf";
		$path = "./uploads/";
		$pdf->Output($path . $filename, 'F');
		//=========================================== send To email ==========================================================================
		$file = $path . $filename;
		$content = file_get_contents($file);
		$contents = chunk_split(base64_encode($content));
		// $uid = md5(uniqid(time()));
		$name = basename($file);

		$subject = "Site Reports for " . $site->site_name;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: yolotelco@gmail.com" . "\r\n";

		// message & attachment
		$body = '<h2 align="center">YOLO-TELCO</h2>
				<br/>Site Reports for' . $site->site_name . '</p>';
		$body .= '<table><tbody>';
		if (!$reports) { } else {
			$body .= "<br>";
			$body .= "<h4>Site Reports for " . $site->site_name . "</h4>";
			$body .= "<br>";
			foreach ($reports as $report) {
				$body .= '<br><tr>';
				if ($report->user_id != 0) {
					$body .= '<td>From: ' . $report->first_name . " " . $report->last_name . '</td>';
				} else {
					$body .= '<td>From: ' . $report->team_name . '</td>';
				}
				$body .= '<td>' . $report->text_report . '</td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$body .= '<td>' . $report->report_date . '</td>';
				$body .= '</tr>';
			}
		}

		// $headers .= "Content-Type: application/octet-stream; name = \"".$filename. "\r\n";
		// $headers .= "Content-Transfer-Encoding: base64\r\n";
		// $headers .= "Content-Disposition: attachment; filename = \"".$filename. "\r\n";
		// $headers .= $contents. "\r\n";
		// echo $to, $subject, $body, $headers;
		if (mail($to, $subject, $body, $headers)) {
			echo 'ok';
		} else {
			return false;
		}
	}

	public function set_task_status_to_completed()
	{
		$this->load->library('session');
		$this->load->model("users");
		if (isset($_POST['site_task_id'])) {
			$site_task_id = $_POST['site_task_id'];
			$user_id = $_POST['user_id'];
			$site_id = $_POST['site_id'];
			$team_id = $_POST['team_id'];
			$site_task = $_POST['site_task'];
			$report_date = date('Y-m-d');

			$user = $this->users->getUser($user_id);
			$team = $this->users->getTeam($team_id);
			if ($user && !$team) {
				$text_report = "User " . $user->first_name . " " . $user->last_name . " Finished the task " . $site_task;
			} else {
				$text_report = "Team " . $team->team_name . " Finished the task " . $site_task;
			}
			$site_tasks = array(
				"task_status" => 1
			);
			$report_data = array(
				"site_task_id" => $site_task_id,
				"user_id" => $user_id,
				"site_id" => $site_id,
				"team_id" => $team_id,
				"text_report" => $text_report,
				"report_date" => $report_date
			);
			print_r($report_data);
			$this->users->update_task_status_to_completed($_POST['task_id'], $site_tasks);
			$this->users->addReport($report_data);
		}
	}

	public function upload_site()
	{
		$this->load->library('session');
		$this->load->model("users");
		if (isset($_FILES['generator_battery_photo_1'])) {
			$generator_battery_photo_1 = $this->_upload_site_image($_FILES['generator_battery_photo_1']);
		} else {
			$generator_battery_photo_1 = '';
		}
		if (isset($_FILES['generator_battery_photo_2'])) {
			$generator_battery_photo_2 = $this->_upload_site_image($_FILES['generator_battery_photo_2']);
		} else {

			$generator_battery_photo_2 = '';
		}
		if (isset($_FILES['generator_battery_photo_3'])) {
			$generator_battery_photo_3 = $this->_upload_site_image($_FILES['generator_battery_photo_3']);
		} else {
			$generator_battery_photo_3 = '';
		}
		if (isset($_FILES['generator_battery_photo_4'])) {
			$generator_battery_photo_4 = $this->_upload_site_image($_FILES['generator_battery_photo_4']);
		} else {
			$generator_battery_photo_4 = '';
		}
		if (isset($_FILES['generator_battery_photo_5'])) {
			$generator_battery_photo_5 = $this->_upload_site_image($_FILES['generator_battery_photo_5']);
		} else {
			$generator_battery_photo_5 = '';
		}
		if (isset($_FILES['generator_battery_photo_6'])) {
			$generator_battery_photo_6 = $this->_upload_site_image($_FILES['generator_battery_photo_6']);
		} else {
			$generator_battery_photo_6 = '';
		}
		if (isset($_FILES['generator_battery_photo_7'])) {
			$generator_battery_photo_7 = $this->_upload_site_image($_FILES['generator_battery_photo_7']);
		} else {
			$generator_battery_photo_7 = '';
		}
		if (isset($_FILES['generator_battery_photo_8'])) {
			$generator_battery_photo_8 = $this->_upload_site_image($_FILES['generator_battery_photo_8']);
		} else {
			$generator_battery_photo_8 = '';
		}

		if (isset($_FILES['add_battery_photo_1'])) {
			$battery_photo_1 = $this->_upload_site_image($_FILES['add_battery_photo_1']);
		} else {
			$battery_photo_1 = '';
		}
		if (isset($_FILES['add_battery_photo_2'])) {
			$battery_photo_2 = $this->_upload_site_image($_FILES['add_battery_photo_2']);
		} else {
			$battery_photo_2 = '';
		}
		if (isset($_FILES['add_battery_photo_3'])) {
			$battery_photo_3 = $this->_upload_site_image($_FILES['add_battery_photo_3']);
		} else {
			$battery_photo_3 = '';
		}
		if (isset($_FILES['add_battery_photo_4'])) {
			$battery_photo_4 = $this->_upload_site_image($_FILES['add_battery_photo_4']);
		} else {
			$battery_photo_4 = '';
		}
		if (isset($_FILES['add_battery_photo_5'])) {
			$battery_photo_5 = $this->_upload_site_image($_FILES['add_battery_photo_5']);
		} else {
			$battery_photo_5 = '';
		}
		if (isset($_FILES['add_battery_photo_6'])) {
			$battery_photo_6 = $this->_upload_site_image($_FILES['add_battery_photo_6']);
		} else {
			$battery_photo_6 = '';
		}
		if (isset($_FILES['add_battery_photo_7'])) {
			$battery_photo_7 = $this->_upload_site_image($_FILES['add_battery_photo_7']);
		} else {
			$battery_photo_7 = '';
		}
		if (isset($_FILES['add_battery_photo_8'])) {
			$battery_photo_8 = $this->_upload_site_image($_FILES['add_battery_photo_8']);
		} else {
			$battery_photo_8 = '';
		}
		if (isset($_FILES['add_battery_photo_9'])) {
			$battery_photo_9 = $this->_upload_site_image($_FILES['add_battery_photo_9']);
		} else {
			$battery_photo_9 = '';
		}
		if (isset($_FILES['add_battery_photo_10'])) {
			$battery_photo_10 = $this->_upload_site_image($_FILES['add_battery_photo_10']);
		} else {
			$battery_photo_10 = '';
		}


		if (isset($_FILES['tank_photo_1'])) {
			$tank_photo_1 = $this->_upload_site_image($_FILES['tank_photo_1']);
		} else {
			$tank_photo_1 = '';
		}
		if (isset($_FILES['tank_photo_2'])) {
			$tank_photo_2 = $this->_upload_site_image($_FILES['tank_photo_2']);
		} else {
			$tank_photo_2 = '';
		}
		if (isset($_FILES['tank_photo_3'])) {
			$tank_photo_3 = $this->_upload_site_image($_FILES['tank_photo_3']);
		} else {
			$tank_photo_3 = '';
		}
		if (isset($_FILES['tank_photo_4'])) {
			$tank_photo_4 = $this->_upload_site_image($_FILES['tank_photo_4']);
		} else {
			$tank_photo_4 = '';
		}
		if (isset($_FILES['tank_photo_5'])) {
			$tank_photo_5 = $this->_upload_site_image($_FILES['tank_photo_5']);
		} else {
			$tank_photo_5 = '';
		}
		if (isset($_FILES['tank_photo_6'])) {
			$tank_photo_6 = $this->_upload_site_image($_FILES['tank_photo_6']);
		} else {
			$tank_photo_6 = '';
		}

		if (isset($_FILES['generator_and_engine_photo_1'])) {
			$generator_and_engine_photo_1 = $this->_upload_site_image($_FILES['generator_and_engine_photo_1']);
		} else {
			$generator_and_engine_photo_1 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_2'])) {
			$generator_and_engine_photo_2 = $this->_upload_site_image($_FILES['generator_and_engine_photo_2']);
		} else {
			$generator_and_engine_photo_2 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_3'])) {
			$generator_and_engine_photo_3 = $this->_upload_site_image($_FILES['generator_and_engine_photo_3']);
		} else {
			$generator_and_engine_photo_3 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_4'])) {
			$generator_and_engine_photo_4 = $this->_upload_site_image($_FILES['generator_and_engine_photo_4']);
		} else {
			$generator_and_engine_photo_4 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_5'])) {
			$generator_and_engine_photo_5 = $this->_upload_site_image($_FILES['generator_and_engine_photo_5']);
		} else {
			$generator_and_engine_photo_5 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_6'])) {
			$generator_and_engine_photo_6 = $this->_upload_site_image($_FILES['generator_and_engine_photo_6']);
		} else {
			$generator_and_engine_photo_6 = '';
		}

		if (isset($_FILES['cylinder_photo_1'])) {
			$cylinder_photo_1 = $this->_upload_site_image($_FILES['cylinder_photo_1']);
		} else {
			$cylinder_photo_1 = '';
		}
		if (isset($_FILES['cylinder_photo_2'])) {
			$cylinder_photo_2 = $this->_upload_site_image($_FILES['cylinder_photo_2']);
		} else {
			$cylinder_photo_2 = '';
		}
		if (isset($_FILES['cylinder_photo_3'])) {
			$cylinder_photo_3 = $this->_upload_site_image($_FILES['cylinder_photo_3']);
		} else {
			$cylinder_photo_3 = '';
		}
		if (isset($_FILES['cylinder_photo_4'])) {
			$cylinder_photo_4 = $this->_upload_site_image($_FILES['cylinder_photo_4']);
		} else {
			$cylinder_photo_4 = '';
		}
		if (isset($_FILES['cylinder_photo_5'])) {
			$cylinder_photo_5 = $this->_upload_site_image($_FILES['cylinder_photo_5']);
		} else {
			$cylinder_photo_5 = '';
		}
		if (isset($_FILES['cylinder_photo_6'])) {
			$cylinder_photo_6 = $this->_upload_site_image($_FILES['cylinder_photo_6']);
		} else {
			$cylinder_photo_6 = '';
		}
		if (isset($_FILES['cylinder_photo_7'])) {
			$cylinder_photo_7 = $this->_upload_site_image($_FILES['cylinder_photo_7']);
		} else {
			$cylinder_photo_7 = '';
		}
		if (isset($_FILES['cylinder_photo_8'])) {
			$cylinder_photo_8 = $this->_upload_site_image($_FILES['cylinder_photo_8']);
		} else {
			$cylinder_photo_8 = '';
		}
		if (isset($_FILES['fueLcell_photo_1'])) {
			$fuel_cell_photo_1 = $this->_upload_site_image($_FILES['fueLcell_photo_1']);
		} else {
			$fuel_cell_photo_1 = '';
		}
		if (isset($_FILES['fueLcell_photo_2'])) {
			$fuel_cell_photo_2 = $this->_upload_site_image($_FILES['fueLcell_photo_2']);
		} else {
			$fuel_cell_photo_2 = '';
		}
		if (isset($_FILES['fueLcell_photo_3'])) {
			$fuel_cell_photo_3 = $this->_upload_site_image($_FILES['fueLcell_photo_3']);
		} else {
			$fuel_cell_photo_3 = '';
		}
		if (isset($_FILES['fueLcell_photo_4'])) {
			$fuel_cell_photo_4 = $this->_upload_site_image($_FILES['fueLcell_photo_4']);
		} else {
			$fuel_cell_photo_4 = '';
		}
		if (isset($_FILES['fueLcell_photo_5'])) {
			$fuel_cell_photo_5 = $this->_upload_site_image($_FILES['fueLcell_photo_5']);
		} else {
			$fuel_cell_photo_5 = '';
		}
		if (isset($_FILES['fueLcell_photo_6'])) {
			$fuel_cell_photo_6 = $this->_upload_site_image($_FILES['fueLcell_photo_6']);
		} else {
			$fuel_cell_photo_6 = '';
		}
		if (isset($_FILES['fueLcell_photo_7'])) {
			$fuel_cell_photo_7 = $this->_upload_site_image($_FILES['fueLcell_photo_7']);
		} else {
			$fuel_cell_photo_7 = '';
		}
		if (isset($_FILES['fueLcell_photo_8'])) {
			$fuel_cell_photo_8 = $this->_upload_site_image($_FILES['fueLcell_photo_8']);
		} else {
			$fuel_cell_photo_8 = '';
		}
		if (isset($_FILES['other_hazardous_material_photo_1'])) {
			$other_hazardous_material_photo_1 = $this->_upload_site_image($_FILES['other_hazardous_material_photo_1']);
		} else {
			$other_hazardous_material_photo_1 = '';
		}
		if (isset($_FILES['other_hazardous_material_photo_2'])) {
			$other_hazardous_material_photo_2 = $this->_upload_site_image($_FILES['other_hazardous_material_photo_2']);
		} else {
			$other_hazardous_material_photo_2 = '';
		}
		if (isset($_POST['certificate_signature']) && $_POST['certificate_signature'] != '') {
			time();
			$certificate_signature =  rand() . '.jpg';
			$filePath = './uploads/' . $certificate_signature;
			$file = fopen($filePath, 'w');
			fwrite($file, base64_decode($_POST['certificate_signature']));
			fclose($file);
		} else {
			$certificate_signature = '';
		}
		$data = array(
			'site_name' => $this->input->post("add_site_name"),
			'attuid' => $this->input->post("add_attuid"),
			'email' => $this->input->post("add_email_address"),
			'group_mailbox' => $this->input->post("add_group_mailbox"),
			'region' => $this->input->post("add_region1"),
			'corporate' => $this->input->post("add_corporate"),
			'facility_region' => $this->input->post("add_region2"),
			'market' => $this->input->post("add_market"),
			'site_id_and_name' => $this->input->post("add_site_id_and_name"),
			'usid' => $this->input->post("add_usid"),
			'fa_location_code' => $this->input->post("add_fa_location_code"),
			'oracle_Project_tracking_number' => $this->input->post("add_oracle_project_tracking_number"),
			'pace_project_tracking_number' => $this->input->post("add_pace_project_tracking_number"),
			'at_and_t_construction_manager' => $this->input->post("add_at_and_t_construction_manager"),
			'strett_address' => $this->input->post("add_street_address"),
			'city' => $this->input->post("add_city"),
			'country' => $this->input->post("add_country"),
			'state' => $this->input->post("add_state"),
			'zip_code' => $this->input->post("add_zip_code"),
			'site_type' => $this->input->post("add_site_type"),
			'site_located_on' => $this->input->post("add_site_located_on"),
			'project_type' => $this->input->post("add_project_type"),
			'hazardous_material_changes_reported' => '' . $this->input->post("Battery") . '-' . $this->input->post("Tank") . '- ' . $this->input->post("Generators") . '- ' . $this->input->post("Cylinder") . '- ' . $this->input->post("Start_up_battery") . '', //''.$this->input->post("Battery").' '.$this->input->post("Tank").' '.$this->input->post("Generators").' '.$this->input->post("Cylinder").' '.$this->input->post("Start_up_battery").'',  // ?? ano ni dapat di erp
			'other_hazardous_material' => $this->input->post("add_other_hazardous_materials"),
			'battery_manufcaturer' => $this->input->post("add_battery_manufacturer"),
			'batterY_model' => $this->input->post("add_battery_model"),
			'battery_list_number' => $this->input->post("add_battery_list_number"),
			'battery_manufacturer_date' => $this->input->post("add_battery_manufacturer_date"),
			'battery_removed_quantity' => $this->input->post("add_removed_battery_unit"),
			'battery_status' => $this->input->post("add_battery_status"),
			'battery_location_discription' => $this->input->post("battery_location_discription"),
			'cabinet_type' => $this->input->post("add_cabinet_type"),
			'cabinet_number' => $this->input->post("add_cabinet_number"),
			'battery_delivery_date' => $this->input->post("add_battery_delivery_date"),
			'battery_installation_date' => $this->input->post("add_battery_installation_date"),
			'vendor_completing_installation' => $this->input->post("add_battery_vendor_completing_installation"),
			// 'battery_removal_date' => $this->input->post("add_removed_battery_removal_date"), //no
			// 'vendor_completing_removal' => $this->input->post("add_removed_vendor_completing_removal"), // no
			'removed_battery_manufacturer' => $this->input->post("add_removed_battery_manufacturer"),
			'removed_battery_model' => $this->input->post("add_removed_battery_model_battery_info"),
			'removed_battery_list_number' => $this->input->post("add_removed_battery_list_number"),
			'removed_battery_manufacturer_date' => $this->input->post("add_removed_battery_manufacturer_date"),
			'removed_battery_removed_quantity' => $this->input->post("add_removed_battery_unit"),
			'removed_battery_status' => $this->input->post("add_remove_battery_status"),
			'removed_battery_location_discription' => $this->input->post("add_removed_battery_location_discription"),
			'removed_cabinet_type' => $this->input->post("add_removed_cabinet_type"),
			'removed_cabinet_number' => $this->input->post("add_removed_cabinet_number"),
			'removed_battery_removal_date' => $this->input->post("add_removed_battery_removal_date"),
			'removed_vendor_completing_removal' => $this->input->post("add_removed_vendor_completing_removal"),
			'existing_battery_manufacturer' => $this->input->post("add_battery_existing_battery_manufacturer"),
			'existing_battery_modal' => $this->input->post("add_battery_existing_battery_model"),
			'existing_battery_list_number' => $this->input->post("add_battery_existing_battery_list_number"),
			'existing_battery_manufacturer_date' => $this->input->post("battery_existing_battery_manufacturer_date"),
			'existing_battery_quantity' => $this->input->post("add_battery_existing_quantity"),
			'existing_battery_status' => $this->input->post("add_battery_existng_battery_status"),
			'existing_battery_location_discription' => $this->input->post("add_battery_existing_location_discription"),
			'existing_cabinet_type' => $this->input->post("add_battery_existing_cabinet_type"),
			'existing_cabinet_number' => $this->input->post("add_battery_existing_cabinet_number"),
			'total_number_of_battery_units_on_site_before_installation' => $this->input->post("add_battery_existing_total_number_of_battery_units_on_site_before_installation"),
			'total_number_of_battery_units_on_site_after_installation' => $this->input->post("add_battery_existing_total_number_of_battery_units_on_site_after_installation"),
			'is_the_position_65_sign_posted_for_the_presence_of_lead_acid' => $this->input->post("add_battery_existing_is_the_proposition_65_posted_for_the_presence_of_lead_acid"),
			'battery_comments' => $this->input->post("add_battery_existing_enter_any_other_comments_that_might_pertain_to_these_batteries"),
			'battery_photo_1' => $battery_photo_1,
			'battery_photo_2' => $battery_photo_2,
			'battery_photo_3' => $battery_photo_3,
			'battery_photo_4' => $battery_photo_4,
			'battery_photo_5' => $battery_photo_5,
			'battery_photo_6' => $battery_photo_6,
			'battery_photo_7' => $battery_photo_7,
			'battery_photo_8' => $battery_photo_8,
			'battery_photo_9' => $battery_photo_9,
			'battery_photo_10' => $battery_photo_10,
			'generator_battery_manufacturer' => $this->input->post("add_generator_battery_manufacturer"),
			'generator_battery_model' => $this->input->post("add_generator_battery_model"),
			'generator_battery_list_number' => $this->input->post("add_generator_battery_list_number"),
			'generator_added_quantity' => $this->input->post("add_generator_added_quanitity"),
			'generator_battery_status' => $this->input->post("add_generator_battery_status"),
			'generator_location_discription' => $this->input->post("add_generator_is_the_position_65"),  //indi kudi sure erp
			'generator_battery_delivery_date' => $this->input->post("add_generator_battery_delivery_date"),
			'generator_battery_installation_date' => $this->input->post("add_generator_battery_installation_date"),
			'generator_vendor_completing_installation' => $this->input->post("add_generator_vendor_completing_installation"),
			'generator_removed_battery_manufacturer' => $this->input->post("add_removed_generator_battery_manufactoruer"),
			'generator_removed_battery_model' => $this->input->post("add_removed_battery_model_generator"),
			'generator_removed_list_number' => $this->input->post("add_removed_generator_battery_list_number"),
			'generator_removed_quantity' => $this->input->post("add_removed_generator_removed_quantity"),
			'generator_removed_battery_status' => $this->input->post("add_removed_generator_battery_status"),
			'generator_removed_location_discription' => $this->input->post("add_removed_generator_location_discription"),
			'generator_removed_date_battery_from_site' => $this->input->post("add_removed_generator_date_battery_removed_from_site"),
			'generator_removed_vendor_completing_removal' => $this->input->post("add_removed_generator_vendor_completing_removal"),
			'genarator_existing_battery_manufacturer' => $this->input->post("add_removed_generator_existing_not_added_or_removed_as_part_of_the_project"),
			'generator_existing_battery_model' => $this->input->post("add_existing_generator_battery_model"),
			'generator_existing_battery_list_number' => $this->input->post("add_existing_generator_battery_list_number"),
			'generator_existing_existing_quanitity' => $this->input->post("add_existing_generator_existing_quantity"),
			'generator_existing_battery_status' => $this->input->post("add_existing_generator_battery_status"),
			'generator_existing_location_discription' => $this->input->post("add_existing_generator_location_discription"),
			'generator_existing_battery_comments' => $this->input->post("add_existing_generator_battey_comments"),
			'generator_battery_photo_1' => $generator_battery_photo_1,
			'generator_battery_photo_2' => $generator_battery_photo_2,
			'generator_battery_photo_3' => $generator_battery_photo_3,
			'generator_battery_photo_4' => $generator_battery_photo_4,
			'generator_battery_photo_5' => $generator_battery_photo_5,
			'generator_battery_photo_6' => $generator_battery_photo_6,
			'generator_battery_photo_7' => $generator_battery_photo_7,
			'generator_battery_photo_8' => $generator_battery_photo_8,
			'fuel_tank_added_Removed_or_existing' => $this->input->post("fuel_tank_tank_added_removed_or_existing"),
			'fuel_tank_status' => $this->input->post("fuel_tank_tank_status"),
			'fuel_tank_type' => $this->input->post("fuel_tank_tank_type"),
			'fuel_tank_portable_fixed' => $this->input->post("fuel_tank_portable_or_fixed"),
			'fuel_tank_type_of_fuel' => $this->input->post("fuel_tank_type_of_fuel"),
			'fuel_tank_total_storage_capacity_of_tank_gallons' => $this->input->post("fuel_tank_total_storage_capacity"),
			'fuel_tank_disciption' => $this->input->post("fuel_tank_description"),
			'fuel_tank_manufacturer' => $this->input->post("fuel_tank_manufacturer"),
			'fuel_tank_serial_number' => $this->input->post("fuel_tank_serial_number"),
			'fuel_tank_tank_manufacturer_date' => $this->input->post("fuel_tank_manufactorer_date"),
			'fuel_tank_delivery_date' => $this->input->post("fuel_tank_delivery_date"),
			'fuel_tank_install_date' => $this->input->post("fuel_tank_install_date"),
			'fuel_tank_vendor_completing_installation' => $this->input->post("fuel_tank_vendor_completing_installation"),
			'fuel_tank_usage' => $this->input->post("fuel_tank_usage"),
			'fuel_tank_usage_status' => $this->input->post("fuel_tank_usage_status"),
			'fuel_tank_indoor_or_outdoor' => $this->input->post("fuel_tank_is_the_tank_indoor_or_outdoor"),
			'fuel_tank_location_discription' => $this->input->post("fuel_tank_location_description"),
			'fuel_tank_structure' => $this->input->post("fuel_tank_structure"),
			'fuel_tank_exterior_wall_structure' => $this->input->post("fue_tank_exterior_wall_structure"),
			'fuel_tank_ownership' => $this->input->post("fuel_tank_ownership"),
			'fuel_tank_owner_name' => $this->input->post("fuel_tank_owner_name"),
			'fuel_tank_first_fuel_date' => $this->input->post("fuel_tank_first_fuel_date"),
			'fuel_tank_removal_date' => $this->input->post("fuel_tank_removal_date"),
			'fuel_tank_vendor_completing_removal' => $this->input->post("fuel_tank_completing_removal"),
			'fuel_tank_was_approved_recieved' => $this->input->post("tank_installation_was_approved"),
			'fuel_tank_date_of_ntp_approval' => $this->input->post("tank_installation_date_of_ntp_approval"),
			'fuel_tank_is_the_task_permitted' => $this->input->post("tank_istallation_is_tank_permitted"),
			'fuel_tank_Permit_type' => $this->input->post("tank_installation_permit_type"),
			'fuel_tank_permitting_agency' => $this->input->post("tank_installation_permitting_agency"),
			'fuel_tank_permit_number' => $this->input->post("tank_installation_permit_number"),
			'fuel_tank_permit_expiration' => $this->input->post("tank_installation_permit_expiration"),
			'fuel_tank_spill_kit' => $this->input->post("tank_spill_kit"),
			'fuel_tank_spill_bucket' => $this->input->post("tank_spill_bucket"),
			'fuel_tank_secondary_containment' => $this->input->post("tank_secondary_containment"),
			'fuel_tank_leak_detection' => $this->input->post("tank_leak_detection"),
			'fuel_tank_overfill_protection' => $this->input->post("tank_overfill_protection"),
			'fuel_tank_cathodic_protection_present' => $this->input->post("tank_cathodic_protection_present"),
			'fuel_tank__safety_post' => $this->input->post("tank_safety_post"),
			'fuel_tank_proper_labeling' => $this->input->post("tank_proper_labeling"),
			'fuel_tank_tertiary_containment' => $this->input->post("tank_tertiary_containment"),
			'fuel_tank_comments' => $this->input->post("tank_comments"),
			'fuel_tank_photo_1' => $tank_photo_1,
			'fuel_tank_photo_2' => $tank_photo_2,
			'fuel_tank_photo_3' => $tank_photo_3,
			'fuel_tank_photo_4' => $tank_photo_4,
			'fuel_tank_photo_5' => $tank_photo_5,
			'fuel_tank_photo_6' => $tank_photo_6,
			'generator_added_removed_or_existing' => $this->input->post("generator_and_engine_generator_added_removed_or_existing"),
			'generator_status' => $this->input->post("generator_and_engine_generator_status"),
			'generator_type' => $this->input->post("generator_and_engine_generator_type"),
			'generator_manufacturer' => $this->input->post("generator_and_engine_generator_manufacturer"),
			'generator_model' => $this->input->post("generator_and_engine_generator_model"),
			'generator_serial_number' => $this->input->post("generator_and_engine_generator_serial_number"),
			'generator_output_rating_value' => $this->input->post("generator_and_engine_output_rating_value_1st"),
			'generator_portable_or_fixed' => $this->input->post("generator_and_engine_portable_or_fixed"),
			'generator_location_description' => $this->input->post("generator_and_engine_portable_or_fixed"),
			'generator_delivery_date' => $this->input->post("generator_and_engine_generator_delivery_date"),
			'generator_installation_date' => $this->input->post("generator_and_engine_generator_installation_date"),
			'generator_manufacturer_date' => $this->input->post("generator_manufacturer_date"),
			'generator_ownership' => $this->input->post("generator_ownership"),
			'generator_and_engine_vendor_completing_installation' => $this->input->post("generator_and_engine_vendor_completing_installation"),
			'generator_removal_date' => $this->input->post("generator_and_engine_generator_removal_date"),
			'generator_vendor_completing_removal' => $this->input->post("generator_and_engine_vendor_completing_removal"),
			'generator_is_generator_prime_power' => $this->input->post("cell_site_is_generator_prime_power"),
			'generator_is_commercial_power_used_at_site' => $this->input->post("cell_site_is_commercial_power_used_at_site"),
			'generator_non_resettable_hour_meter' => $this->input->post("cell_site_none_resettable_hour_meter"),
			'generator_current_hour_meter_reading' => $this->input->post("cell_site_current_hour_meter_reader"),
			'generator_date_hour_meter_reading' => $this->input->post("cell_site_date_of_hour_meter_reader"),
			'generator_date_of_first_start_up_first_fire' => $this->input->post("cell_site_date_of_first_start_up"),
			'generator_is_there_a_generator_on_site_used_for_emergency_power' => $this->input->post("cell_site_is_there_generator_on_site_used_for_emergency_power"),
			'generator_comments' => $this->input->post("generator_comments"),
			'engine_manufacturer' => $this->input->post("engine_manufacturer"),
			'engine_model' => $this->input->post("engine_model"),
			'engine_serial_number' => $this->input->post("engine_serial_number"),
			'engine_type' => $this->input->post("engine_type"),
			'engine_type_of_fuel' => $this->input->post("engine_type_of_fuel"),
			'engine_manufacturer_date' => $this->input->post("engine_manufacturer_date"),
			'engine_install_date' => $this->input->post("engine_install_date"),
			'engine_number_of_cylinders' => $this->input->post("engine_number_of_cylinders"),
			'engine_max_engine_kilowatts' => $this->input->post("engine_max_engine_kilowatts"),
			'engine_rated_horsepower' => $this->input->post("engine_rated_horsepower"),
			'engine_max_brake_horsepower' => $this->input->post("engine_max_brake_horsepower"),
			'engine_displacement_liters_or_cubic_inches' => $this->input->post("engine_displacement_liters_or_cubic_inches"),
			'engine_epa_family_number' => $this->input->post("engine_epa_family_number"),
			'engine_diameter_of_stack_outlet_inches' => $this->input->post("engine_diameter_of_stack_outlet"),
			'engine__exhaust_stack_height_from_ground' => $this->input->post("engine_exhaust_stack_height_from_ground"),
			'engine_direction_of_exhouse_outlet' => $this->input->post("engine_direction_of_exhaust_outlet"),
			'engine_end_of_stack' => $this->input->post("end_of_stack_open_or_capped"),  //erp ari pgd
			'generator_was_approval_recieved_from_eh_and_s' => $this->input->post("general_installation_was_approval_received"),
			'generator_date_of_ntp_approval' => $this->input->post("general_installation_date_of_ntp_approval"),
			'generator_is_the_generator_or_engine_permitted' => $this->input->post("generator_installation_is_the_generator_or_engine_permitted"),
			'generator_permit_type' => $this->input->post("generator_installation_permit_type"),
			'generator_permitting_agency' => $this->input->post("generator_installation_permitting_agency"),
			'generator_permit_number' => $this->input->post("generator_installation_permit_number"),
			'generator_permit_expiration' => $this->input->post("generator_permit_expiration"),   //ari pgd erp
			'generator_distance_from_engine_to_nearest_fence_line' => $this->input->post("general_distance_distance_from_engine_to_nearest_fence_line"),
			'generator_distance_from_engine_to_nearest_residence' => $this->input->post("general_distance_distance_from_engine_to_nearest_residence"),
			'generator_distance_from_engine_to_nearest_business' => $this->input->post("general_distance_from_engine_to_nearest_business"),
			'generator_is_engine_located_within_500_feet_of_from_a_school' => $this->input->post("generator_distance_is_engine_located_within_500_feet"),
			'generator_if_yes_name_of_facility' => $this->input->post("generator_distance_if_yes_name_of_facility"),
			'generator_if_yes_distance' => $this->input->post("generator_distance_if_yes_distance"),
			'engine_direct_injection' => $this->input->post("where_applicable_direct_injection"),
			'engine_diesel_particular_filter' => $this->input->post("where_applicable_diesel_particulate_filter"),
			'engine_injection_time_retard' => $this->input->post("where_applicable_injection_time_retard"),
			'engine_other_emission_controls' => $this->input->post("where_applicable_other_emission_controls"),
			'engine_turbo_charger' => $this->input->post("where_applicable_turbo_charger"),
			'engine_diesel_oxidation_catalyst' => $this->input->post("where_applicable_diesel_oxidation_catalyst"),
			'engine_catalytic_converter' => $this->input->post("where_applicable_catalytic_converter"),
			'engine_intercooler' => $this->input->post("where_applicable_intercooler"),
			'engine_aftercooler' => $this->input->post("where_applicable_after_cooler"),
			'engine_comments' => $this->input->post("engine_commets"),
			'generator_and_engine_photo_1' => $generator_and_engine_photo_1,
			'generator_and_engine_photo_2' => $generator_and_engine_photo_2,
			'generator_and_engine_photo_3' => $generator_and_engine_photo_3,
			'generator_and_engine_photo_4' => $generator_and_engine_photo_4,
			'generator_and_engine_photo_5' => $generator_and_engine_photo_5,
			'generator_and_engine_photo_6' => $generator_and_engine_photo_6,
			'cylinder_status' => $this->input->post("cylinder_status"),
			'cylinder_contents' => $this->input->post("cylinder_contents"),
			'cylinder_total_cylinder_volume' => $this->input->post("cylinder_total_cylinder_volume"),
			'cylinder_number_of_cylinder_installed' => $this->input->post("cylinder_number_of_cylinders_installsed"),
			'cylinder_largiest_cylinder_volume' => $this->input->post("cylinder_largest_cylinder_volume"),
			'cylinder_date_cylinder_delivered' => $this->input->post("cylinder_date_cylinders_delivered"),
			'cylinder_date_cylinder_installed' => $this->input->post("cylinder_date_cylinders_installed"),
			'cylinder_existing_quantiity' => $this->input->post("cylinder_existing_quantity"),
			'cylinder_location_of_cylinders' => $this->input->post("location_cylinders"),
			'cylinder_vendor_completing_installation' => $this->input->post("vendor_completing_installation"),

			'cylinder_removed_cylinder_status' => $this->input->post("cylinder_removed_cylinder_status"),
			'cylinder_removed_cylinder_contents' => $this->input->post("cylinder_removed_cylinder_contents"),
			'cylinder_removed_total_cylinder_volume' => $this->input->post("cylinder_removed_total_cylinder_volume"),
			'cylinder_removed_largest_cylinder_volume' => $this->input->post("cylinder_removed_largest_cylinder_volume"),
			'cylinder_removed_quantity' => $this->input->post("cylinder_removed_quantity"),
			'cylinder_removed_date_cylinders_removed' => $this->input->post("cylinder_removed_date_cylinders_removed"),
			'cylinder_removed_location_of_cylinders' => $this->input->post("cylinder_removed_location_of_cylinders"),
			'cylinder_removed_vendor_completing_removal' => $this->input->post("cylinder_removed_vendor_completing_removal"),
			'cylinder_existing_cylinder_status' => $this->input->post("cylinder_existing_cylinder_status"),
			'cylinder_existing_cylinder_contents' => $this->input->post("cylinder_existing_cylinder_contents"),
			'cylinder_existing_total_cylinder_volume' => $this->input->post("cylinder_existing_total_cylinder_volume"),
			'cylinder_existing_largest_cylinder_volume' => $this->input->post("cylinder_existing_largest_cylinder_volume"),
			'cylinder_existing_location_of_cylinder' => $this->input->post("cylinder_existing_location_of_cylinder"),



			'cylinder_total_number_of_cylinder_units_on_site' => $this->input->post("cylinder_existing_total_number_of_cylinder_units"),
			'cylinder_total_number_cylinder_units_on_site_after_installation' => $this->input->post("cylinder_existing_total_number_of_cylinder_units_on_site"),
			'cylinder_comments' => $this->input->post("cylinder_comments"),
			'cylinder_photo_1' => $cylinder_photo_1,
			'cylinder_photo_2' => $cylinder_photo_2,
			'cylinder_photo_3' => $cylinder_photo_3,
			'cylinder_photo_4' => $cylinder_photo_4,
			'cylinder_photo_5' => $cylinder_photo_5,
			'cylinder_photo_6' => $cylinder_photo_6,
			'cylinder_photo_7' => $cylinder_photo_7,
			'cylinder_photo_8' => $cylinder_photo_8,
			'fuel_cell_status' => $this->input->post("fuel_cell_status"),
			'fuel_cell_manufacturer' => $this->input->post("fuel_cell_manufacturer"),
			'fuel_cell_model' => $this->input->post("fuel_cell_model"),
			'fuel_cell_cabinet_serial_number' => $this->input->post("fuel_cell_cabinet_serial_number"),
			'fuel_cell_date_fuel_cell_delivery_to_site' => $this->input->post("fuel_cell_date_fuel_cell_delivered_to_site"),
			'fuel_cell_date_fuel_cell_installed' => $this->input->post("fuel_cell_date_fuel_cell_installed"),
			'fuel_cell_location_discription' => $this->input->post("fuel_cell_location_discription"),
			'fuel_cell_vendor_completing_installation' => $this->input->post("fuel_cell_vendor_completing_installation"),
			'fuel_cell_removed_fuel_cell_status' => $this->input->post("fuel_cell_removed_fuel_cell_status"),
			'fuel_cell_removed_fuel_cell_manufacturer' => $this->input->post("fuel_cell_removed__manufacturer"),
			'fuel_cell_removed_fuel_cell_model' => $this->input->post("fuel_cell_removed_fuel_cell_model"),
			'fuel_cell_removed_cabinet_serial_number' => $this->input->post("fuel_cell_removed_fuel_cell_cabinet_serial_number"),
			'fuel_cell_removed_date_fuel_cell_remove_from_site' => $this->input->post("fuel_cell_removed_date_fuel_cell_removed_from_site"),
			'fuel_cell_removed_location_discription' => $this->input->post("fuel_cell_removed_location_description"),
			'fuel_cell_removed_vendor_completing_removal' => $this->input->post("fuel_cell_removed_vendor_completing_removal"),
			'fuel_cell_existing_fuel_cell_status' => $this->input->post("fuel_cell_existing_fuel_cell_status"),
			'fuel_cell_existing__fuel_cell_manufacturer' => $this->input->post("fuel_cell_existing_fuel_cell_manufacturer"),
			'fuel_cell_existing_fuel_cell_model' => $this->input->post("fuel_cell_existing_fuel_cell_model"),
			'fuel_cell_existing_fuel_cell_cabinet_serial_number' => $this->input->post("fuel_cell_existing_fuel_cell_cabinet_serial_number"),
			'fuel_cell_existing_gas_cylinder_cabinet_serial_number' => $this->input->post("fuel_cell_existing_gas_cylinder_cabinet_serial_number"),
			'fuel_cell_existing_location_discription' => $this->input->post("fuel_cell_existing_location_description"),
			'fuel_cell_comments' => $this->input->post("fuel_cell_comments"),
			'fuel_cell_photo_1' => $fuel_cell_photo_1,
			'fuel_cell_photo_2' => $fuel_cell_photo_2,
			'fuel_cell_photo_3' => $fuel_cell_photo_3,
			'fuel_cell_photo_4' => $fuel_cell_photo_4,
			'fuel_cell_photo_5' => $fuel_cell_photo_5,
			'fuel_cell_photo_6' => $fuel_cell_photo_6,
			'fuel_cell_photo_7' => $fuel_cell_photo_7,
			'fuel_cell_photo_8' => $fuel_cell_photo_8,
			'other_hazardous_material_other_hazmat_add_removed_or_existing' => $this->input->post("other_hazarous_other_hazmat_added_removed_or_existing"),
			'other_hazardous_material_hazardous_material' => $this->input->post("other_hazarous_hazardous_material"),
			'other_hazardous_material_container_location' => $this->input->post("other_hazarous_location_description"),
			'other_hazardous_material_container_type' => $this->input->post("other_hazarous_container_type"),
			'other_hazardous_material_container_size' => $this->input->post("other_hazarous_container_size"),
			'other_hazardous_material_number_of_containers' => $this->input->post("number_of_container"),
			'other_hazardous_material_inventory_delivery_date' => $this->input->post("other_hazarous_inventory_delivery"),
			'other_hazardous_material_inventory_installation_date' => $this->input->post("other_hazarous_inventory_installation_date"),
			'other_hazardous_material_inventory_removal_date' => $this->input->post("other_hazarous_inventory_removal_date"),
			'other_hazardous_material_does_this_site_have_freon' => $this->input->post("refrigerants_does_this_site_have_freon"),
			'other_hazardous_material_if_so_how_much_total_freon' => $this->input->post("refrigerants_if_so_how_much_total_freon_is_at_the_location"),
			'other_hazardous_material_other_hazmat_comments' => $this->input->post("refrigerants_other_hazmat_comments"),
			'required_hazardous_material_photo_1' => $other_hazardous_material_photo_1,
			'required_hazardous_material_photo_2' => $other_hazardous_material_photo_2,
			'certification_name' => $this->input->post("certification_name"),
			'certification_signature' => $certificate_signature,
			'certification_at_and_t_project_manager' => $this->input->post("certification_at_and_t_manager"),
			'certification_company_vendor_responsible_for_work' => $this->input->post("certification_company_vendor"),
			'certification_telephone_number_and_or_email' => $this->input->post("certification_telephone_number_and_or_email"),
			'certification_date' => $this->input->post("certification_date")

		);
		$insert = $this->users->add_sites($data);
		$site_id = $this->users->lastID();
		$this->_site_pdf($site_id);
	}

	public function update_site()
	{
		$this->load->library('session');
		$this->load->model("users");
		$site_id = $_POST['site_id'];
		$data = array(
			'site_name' => $this->input->post("add_site_name"),
			'attuid' => $this->input->post("add_attuid"),
			'email' => $this->input->post("add_email_address"),
			'group_mailbox' => $this->input->post("add_group_mailbox"),
			'region' => $this->input->post("add_region1"),
			'corporate' => $this->input->post("add_corporate"),
			'facility_region' => $this->input->post("add_region2"),
			'market' => $this->input->post("add_market"),
			'site_id_and_name' => $this->input->post("add_site_id_and_name"),
			'usid' => $this->input->post("add_usid"),
			'fa_location_code' => $this->input->post("add_fa_location_code"),
			'oracle_Project_tracking_number' => $this->input->post("add_oracle_project_tracking_number"),
			'pace_project_tracking_number' => $this->input->post("add_pace_project_tracking_number"),
			'at_and_t_construction_manager' => $this->input->post("add_at_and_t_construction_manager"),
			'strett_address' => $this->input->post("add_street_address"),
			'city' => $this->input->post("add_city"),
			'country' => $this->input->post("add_country"),
			'state' => $this->input->post("add_state"),
			'zip_code' => $this->input->post("add_zip_code"),
			'site_type' => $this->input->post("add_site_type"),
			'site_located_on' => $this->input->post("add_site_located_on"),
			'project_type' => $this->input->post("add_project_type"),
			'hazardous_material_changes_reported' => '' . $this->input->post("Battery") . '-' . $this->input->post("Tank") . '- ' . $this->input->post("Generators") . '- ' . $this->input->post("Cylinder") . '- ' . $this->input->post("Start_up_battery") . '', //''.$this->input->post("Battery").' '.$this->input->post("Tank").' '.$this->input->post("Generators").' '.$this->input->post("Cylinder").' '.$this->input->post("Start_up_battery").'',  // ?? ano ni dapat di erp
			'other_hazardous_material' => $this->input->post("add_other_hazardous_materials"),
			'battery_manufcaturer' => $this->input->post("add_battery_manufacturer"),
			'batterY_model' => $this->input->post("add_battery_model"),
			'battery_list_number' => $this->input->post("add_battery_list_number"),
			'battery_manufacturer_date' => $this->input->post("add_battery_manufacturer_date"),
			'battery_removed_quantity' => $this->input->post("add_removed_battery_unit"),
			'battery_status' => $this->input->post("add_battery_status"),
			'battery_location_discription' => $this->input->post("battery_location_discription"),
			'cabinet_type' => $this->input->post("add_cabinet_type"),
			'cabinet_number' => $this->input->post("add_cabinet_number"),
			'battery_delivery_date' => $this->input->post("add_battery_delivery_date"),
			'battery_installation_date' => $this->input->post("add_battery_installation_date"),
			'vendor_completing_installation' => $this->input->post("add_battery_vendor_completing_installation"),
			// 'battery_removal_date' => $this->input->post("add_removed_battery_removal_date"), //no
			// 'vendor_completing_removal' => $this->input->post("add_removed_vendor_completing_removal"), // no
			'removed_battery_manufacturer' => $this->input->post("add_removed_battery_manufacturer"),
			'removed_battery_model' => $this->input->post("add_removed_battery_model_battery_info"),
			'removed_battery_list_number' => $this->input->post("add_removed_battery_list_number"),
			'removed_battery_manufacturer_date' => $this->input->post("add_removed_battery_manufacturer_date"),
			'removed_battery_removed_quantity' => $this->input->post("add_removed_battery_unit"),
			'removed_battery_status' => $this->input->post("add_remove_battery_status"),
			'removed_battery_location_discription' => $this->input->post("add_removed_battery_location_discription"),
			'removed_cabinet_type' => $this->input->post("add_removed_cabinet_type"),
			'removed_cabinet_number' => $this->input->post("add_removed_cabinet_number"),
			'removed_battery_removal_date' => $this->input->post("add_removed_battery_removal_date"),
			'removed_vendor_completing_removal' => $this->input->post("add_removed_vendor_completing_removal"),
			'existing_battery_manufacturer' => $this->input->post("add_battery_existing_battery_manufacturer"),
			'existing_battery_modal' => $this->input->post("add_battery_existing_battery_model"),
			'existing_battery_list_number' => $this->input->post("add_battery_existing_battery_list_number"),
			'existing_battery_manufacturer_date' => $this->input->post("battery_existing_battery_manufacturer_date"),
			'existing_battery_quantity' => $this->input->post("add_battery_existing_quantity"),
			'existing_battery_status' => $this->input->post("add_battery_existng_battery_status"),
			'existing_battery_location_discription' => $this->input->post("add_battery_existing_location_discription"),
			'existing_cabinet_type' => $this->input->post("add_battery_existing_cabinet_type"),
			'existing_cabinet_number' => $this->input->post("add_battery_existing_cabinet_number"),
			'total_number_of_battery_units_on_site_before_installation' => $this->input->post("add_battery_existing_total_number_of_battery_units_on_site_before_installation"),
			'total_number_of_battery_units_on_site_after_installation' => $this->input->post("add_battery_existing_total_number_of_battery_units_on_site_after_installation"),
			'is_the_position_65_sign_posted_for_the_presence_of_lead_acid' => $this->input->post("add_battery_existing_is_the_proposition_65_posted_for_the_presence_of_lead_acid"),
			'battery_comments' => $this->input->post("add_battery_existing_enter_any_other_comments_that_might_pertain_to_these_batteries"),
			'generator_battery_manufacturer' => $this->input->post("add_generator_battery_manufacturer"),
			'generator_battery_model' => $this->input->post("add_generator_battery_model"),
			'generator_battery_list_number' => $this->input->post("add_generator_battery_list_number"),
			'generator_added_quantity' => $this->input->post("add_generator_added_quanitity"),
			'generator_battery_status' => $this->input->post("add_generator_battery_status"),
			'generator_location_discription' => $this->input->post("add_generator_is_the_position_65"),  //indi kudi sure erp
			'generator_battery_delivery_date' => $this->input->post("add_generator_battery_delivery_date"),
			'generator_battery_installation_date' => $this->input->post("add_generator_battery_installation_date"),
			'generator_vendor_completing_installation' => $this->input->post("add_generator_vendor_completing_installation"),
			'generator_removed_battery_manufacturer' => $this->input->post("add_removed_generator_battery_manufactoruer"),
			'generator_removed_battery_model' => $this->input->post("add_removed_battery_model_generator"),
			'generator_removed_list_number' => $this->input->post("add_removed_generator_battery_list_number"),
			'generator_removed_quantity' => $this->input->post("add_removed_generator_removed_quantity"),
			'generator_removed_battery_status' => $this->input->post("add_removed_generator_battery_status"),
			'generator_removed_location_discription' => $this->input->post("add_removed_generator_location_discription"),
			'generator_removed_date_battery_from_site' => $this->input->post("add_removed_generator_date_battery_removed_from_site"),
			'generator_removed_vendor_completing_removal' => $this->input->post("add_removed_generator_vendor_completing_removal"),
			'genarator_existing_battery_manufacturer' => $this->input->post("add_removed_generator_existing_not_added_or_removed_as_part_of_the_project"),
			'generator_existing_battery_model' => $this->input->post("add_existing_generator_battery_model"),
			'generator_existing_battery_list_number' => $this->input->post("add_existing_generator_battery_list_number"),
			'generator_existing_existing_quanitity' => $this->input->post("add_existing_generator_existing_quantity"),
			'generator_existing_battery_status' => $this->input->post("add_existing_generator_battery_status"),
			'generator_existing_location_discription' => $this->input->post("add_existing_generator_location_discription"),
			'generator_existing_battery_comments' => $this->input->post("add_existing_generator_battey_comments"),
			'fuel_tank_added_Removed_or_existing' => $this->input->post("fuel_tank_tank_added_removed_or_existing"),
			'fuel_tank_status' => $this->input->post("fuel_tank_tank_status"),
			'fuel_tank_type' => $this->input->post("fuel_tank_tank_type"),
			'fuel_tank_portable_fixed' => $this->input->post("fuel_tank_portable_or_fixed"),
			'fuel_tank_type_of_fuel' => $this->input->post("fuel_tank_type_of_fuel"),
			'fuel_tank_total_storage_capacity_of_tank_gallons' => $this->input->post("fuel_tank_total_storage_capacity"),
			'fuel_tank_disciption' => $this->input->post("fuel_tank_description"),
			'fuel_tank_manufacturer' => $this->input->post("fuel_tank_manufacturer"),
			'fuel_tank_serial_number' => $this->input->post("fuel_tank_serial_number"),
			'fuel_tank_tank_manufacturer_date' => $this->input->post("fuel_tank_manufactorer_date"),
			'fuel_tank_delivery_date' => $this->input->post("fuel_tank_delivery_date"),
			'fuel_tank_install_date' => $this->input->post("fuel_tank_install_date"),
			'fuel_tank_vendor_completing_installation' => $this->input->post("fuel_tank_vendor_completing_installation"),
			'fuel_tank_usage' => $this->input->post("fuel_tank_usage"),
			'fuel_tank_usage_status' => $this->input->post("fuel_tank_usage_status"),
			'fuel_tank_indoor_or_outdoor' => $this->input->post("fuel_tank_is_the_tank_indoor_or_outdoor"),
			'fuel_tank_location_discription' => $this->input->post("fuel_tank_location_description"),
			'fuel_tank_structure' => $this->input->post("fuel_tank_structure"),
			'fuel_tank_exterior_wall_structure' => $this->input->post("fue_tank_exterior_wall_structure"),
			'fuel_tank_ownership' => $this->input->post("fuel_tank_ownership"),
			'fuel_tank_owner_name' => $this->input->post("fuel_tank_owner_name"),
			'fuel_tank_first_fuel_date' => $this->input->post("fuel_tank_first_fuel_date"),
			'fuel_tank_removal_date' => $this->input->post("fuel_tank_removal_date"),
			'fuel_tank_vendor_completing_removal' => $this->input->post("fuel_tank_completing_removal"),
			'fuel_tank_was_approved_recieved' => $this->input->post("tank_installation_was_approved"),
			'fuel_tank_date_of_ntp_approval' => $this->input->post("tank_installation_date_of_ntp_approval"),
			'fuel_tank_is_the_task_permitted' => $this->input->post("tank_istallation_is_tank_permitted"),
			'fuel_tank_Permit_type' => $this->input->post("tank_installation_permit_type"),
			'fuel_tank_permitting_agency' => $this->input->post("tank_installation_permitting_agency"),
			'fuel_tank_permit_number' => $this->input->post("tank_installation_permit_number"),
			'fuel_tank_permit_expiration' => $this->input->post("tank_installation_permit_expiration"),
			'fuel_tank_spill_kit' => $this->input->post("tank_spill_kit"),
			'fuel_tank_spill_bucket' => $this->input->post("tank_spill_bucket"),
			'fuel_tank_secondary_containment' => $this->input->post("tank_secondary_containment"),
			'fuel_tank_leak_detection' => $this->input->post("tank_leak_detection"),
			'fuel_tank_overfill_protection' => $this->input->post("tank_overfill_protection"),
			'fuel_tank_cathodic_protection_present' => $this->input->post("tank_cathodic_protection_present"),
			'fuel_tank__safety_post' => $this->input->post("tank_safety_post"),
			'fuel_tank_proper_labeling' => $this->input->post("tank_proper_labeling"),
			'fuel_tank_tertiary_containment' => $this->input->post("tank_tertiary_containment"),
			'fuel_tank_comments' => $this->input->post("tank_comments"),

			'generator_added_removed_or_existing' => $this->input->post("generator_and_engine_generator_added_removed_or_existing"),
			'generator_status' => $this->input->post("generator_and_engine_generator_status"),
			'generator_type' => $this->input->post("generator_and_engine_generator_type"),
			'generator_manufacturer' => $this->input->post("generator_and_engine_generator_manufacturer"),
			'generator_model' => $this->input->post("generator_and_engine_generator_model"),
			'generator_serial_number' => $this->input->post("generator_and_engine_generator_serial_number"),
			'generator_output_rating_value' => $this->input->post("generator_and_engine_output_rating_value_1st"),
			'generator_portable_or_fixed' => $this->input->post("generator_and_engine_portable_or_fixed"),
			'generator_location_description' => $this->input->post("generator_and_engine_portable_or_fixed"),
			'generator_delivery_date' => $this->input->post("generator_and_engine_generator_delivery_date"),
			'generator_installation_date' => $this->input->post("generator_and_engine_generator_installation_date"),
			'generator_manufacturer_date' => $this->input->post("generator_manufacturer_date"),
			'generator_ownership' => $this->input->post("generator_ownership"),
			'generator_and_engine_vendor_completing_installation' => $this->input->post("generator_and_engine_vendor_completing_installation"),
			'generator_removal_date' => $this->input->post("generator_and_engine_generator_removal_date"),
			'generator_vendor_completing_removal' => $this->input->post("generator_and_engine_vendor_completing_removal"),
			'generator_is_generator_prime_power' => $this->input->post("cell_site_is_generator_prime_power"),
			'generator_is_commercial_power_used_at_site' => $this->input->post("cell_site_is_commercial_power_used_at_site"),
			'generator_non_resettable_hour_meter' => $this->input->post("cell_site_none_resettable_hour_meter"),
			'generator_current_hour_meter_reading' => $this->input->post("cell_site_current_hour_meter_reader"),
			'generator_date_hour_meter_reading' => $this->input->post("cell_site_date_of_hour_meter_reader"),
			'generator_date_of_first_start_up_first_fire' => $this->input->post("cell_site_date_of_first_start_up"),
			'generator_is_there_a_generator_on_site_used_for_emergency_power' => $this->input->post("cell_site_is_there_generator_on_site_used_for_emergency_power"),
			'generator_comments' => $this->input->post("generator_comments"),
			'engine_manufacturer' => $this->input->post("engine_manufacturer"),
			'engine_model' => $this->input->post("engine_model"),
			'engine_serial_number' => $this->input->post("engine_serial_number"),
			'engine_type' => $this->input->post("engine_type"),
			'engine_type_of_fuel' => $this->input->post("engine_type_of_fuel"),
			'engine_manufacturer_date' => $this->input->post("engine_manufacturer_date"),
			'engine_install_date' => $this->input->post("engine_install_date"),
			'engine_number_of_cylinders' => $this->input->post("engine_number_of_cylinders"),
			'engine_max_engine_kilowatts' => $this->input->post("engine_max_engine_kilowatts"),
			'engine_rated_horsepower' => $this->input->post("engine_rated_horsepower"),
			'engine_max_brake_horsepower' => $this->input->post("engine_max_brake_horsepower"),
			'engine_displacement_liters_or_cubic_inches' => $this->input->post("engine_displacement_liters_or_cubic_inches"),
			'engine_epa_family_number' => $this->input->post("engine_epa_family_number"),
			'engine_diameter_of_stack_outlet_inches' => $this->input->post("engine_diameter_of_stack_outlet"),
			'engine__exhaust_stack_height_from_ground' => $this->input->post("engine_exhaust_stack_height_from_ground"),
			'engine_direction_of_exhouse_outlet' => $this->input->post("engine_direction_of_exhaust_outlet"),
			'engine_end_of_stack' => $this->input->post("end_of_stack_open_or_capped"),  //erp ari pgd
			'generator_was_approval_recieved_from_eh_and_s' => $this->input->post("general_installation_was_approval_received"),
			'generator_date_of_ntp_approval' => $this->input->post("general_installation_date_of_ntp_approval"),
			'generator_is_the_generator_or_engine_permitted' => $this->input->post("generator_installation_is_the_generator_or_engine_permitted"),
			'generator_permit_type' => $this->input->post("generator_installation_permit_type"),
			'generator_permitting_agency' => $this->input->post("generator_installation_permitting_agency"),
			'generator_permit_number' => $this->input->post("generator_installation_permit_number"),
			'generator_permit_expiration' => $this->input->post("generator_permit_expiration"),   //ari pgd erp
			'generator_distance_from_engine_to_nearest_fence_line' => $this->input->post("general_distance_distance_from_engine_to_nearest_fence_line"),
			'generator_distance_from_engine_to_nearest_residence' => $this->input->post("general_distance_distance_from_engine_to_nearest_residence"),
			'generator_distance_from_engine_to_nearest_business' => $this->input->post("general_distance_from_engine_to_nearest_business"),
			'generator_is_engine_located_within_500_feet_of_from_a_school' => $this->input->post("generator_distance_is_engine_located_within_500_feet"),
			'generator_if_yes_name_of_facility' => $this->input->post("generator_distance_if_yes_name_of_facility"),
			'generator_if_yes_distance' => $this->input->post("generator_distance_if_yes_distance"),
			'engine_direct_injection' => $this->input->post("where_applicable_direct_injection"),
			'engine_diesel_particular_filter' => $this->input->post("where_applicable_diesel_particulate_filter"),
			'engine_injection_time_retard' => $this->input->post("where_applicable_injection_time_retard"),
			'engine_other_emission_controls' => $this->input->post("where_applicable_other_emission_controls"),
			'engine_turbo_charger' => $this->input->post("where_applicable_turbo_charger"),
			'engine_diesel_oxidation_catalyst' => $this->input->post("where_applicable_diesel_oxidation_catalyst"),
			'engine_catalytic_converter' => $this->input->post("where_applicable_catalytic_converter"),
			'engine_intercooler' => $this->input->post("where_applicable_intercooler"),
			'engine_aftercooler' => $this->input->post("where_applicable_after_cooler"),
			'engine_comments' => $this->input->post("engine_commets"),
			'cylinder_status' => $this->input->post("cylinder_status"),
			'cylinder_contents' => $this->input->post("cylinder_contents"),
			'cylinder_total_cylinder_volume' => $this->input->post("cylinder_total_cylinder_volume"),
			'cylinder_number_of_cylinder_installed' => $this->input->post("cylinder_number_of_cylinders_installsed"),
			'cylinder_largiest_cylinder_volume' => $this->input->post("cylinder_largest_cylinder_volume"),
			'cylinder_date_cylinder_delivered' => $this->input->post("cylinder_date_cylinders_delivered"),
			'cylinder_date_cylinder_installed' => $this->input->post("cylinder_date_cylinders_installed"),
			'cylinder_existing_quantiity' => $this->input->post("cylinder_existing_quantity"),
			'cylinder_location_of_cylinders' => $this->input->post("location_cylinders"),
			'cylinder_vendor_completing_installation' => $this->input->post("vendor_completing_installation"),

			'cylinder_removed_cylinder_status' => $this->input->post("cylinder_removed_cylinder_status"),
			'cylinder_removed_cylinder_contents' => $this->input->post("cylinder_removed_cylinder_contents"),
			'cylinder_removed_total_cylinder_volume' => $this->input->post("cylinder_removed_total_cylinder_volume"),
			'cylinder_removed_largest_cylinder_volume' => $this->input->post("cylinder_removed_largest_cylinder_volume"),
			'cylinder_removed_quantity' => $this->input->post("cylinder_removed_quantity"),
			'cylinder_removed_date_cylinders_removed' => $this->input->post("cylinder_removed_date_cylinders_removed"),
			'cylinder_removed_location_of_cylinders' => $this->input->post("cylinder_removed_location_of_cylinders"),
			'cylinder_removed_vendor_completing_removal' => $this->input->post("cylinder_removed_vendor_completing_removal"),
			'cylinder_existing_cylinder_status' => $this->input->post("cylinder_existing_cylinder_status"),
			'cylinder_existing_cylinder_contents' => $this->input->post("cylinder_existing_cylinder_contents"),
			'cylinder_existing_total_cylinder_volume' => $this->input->post("cylinder_existing_total_cylinder_volume"),
			'cylinder_existing_largest_cylinder_volume' => $this->input->post("cylinder_existing_largest_cylinder_volume"),
			'cylinder_existing_location_of_cylinder' => $this->input->post("cylinder_existing_location_of_cylinder"),



			'cylinder_total_number_of_cylinder_units_on_site' => $this->input->post("cylinder_existing_total_number_of_cylinder_units"),
			'cylinder_total_number_cylinder_units_on_site_after_installation' => $this->input->post("cylinder_existing_total_number_of_cylinder_units_on_site"),
			'cylinder_comments' => $this->input->post("cylinder_comments"),
			'fuel_cell_status' => $this->input->post("fuel_cell_status"),
			'fuel_cell_manufacturer' => $this->input->post("fuel_cell_manufacturer"),
			'fuel_cell_model' => $this->input->post("fuel_cell_model"),
			'fuel_cell_cabinet_serial_number' => $this->input->post("fuel_cell_cabinet_serial_number"),
			'fuel_cell_date_fuel_cell_delivery_to_site' => $this->input->post("fuel_cell_date_fuel_cell_delivered_to_site"),
			'fuel_cell_date_fuel_cell_installed' => $this->input->post("fuel_cell_date_fuel_cell_installed"),
			'fuel_cell_location_discription' => $this->input->post("fuel_cell_location_discription"),
			'fuel_cell_vendor_completing_installation' => $this->input->post("fuel_cell_vendor_completing_installation"),
			'fuel_cell_removed_fuel_cell_status' => $this->input->post("fuel_cell_removed_fuel_cell_status"),
			'fuel_cell_removed_fuel_cell_manufacturer' => $this->input->post("fuel_cell_removed__manufacturer"),
			'fuel_cell_removed_fuel_cell_model' => $this->input->post("fuel_cell_removed_fuel_cell_model"),
			'fuel_cell_removed_cabinet_serial_number' => $this->input->post("fuel_cell_removed_fuel_cell_cabinet_serial_number"),
			'fuel_cell_removed_date_fuel_cell_remove_from_site' => $this->input->post("fuel_cell_removed_date_fuel_cell_removed_from_site"),
			'fuel_cell_removed_location_discription' => $this->input->post("fuel_cell_removed_location_description"),
			'fuel_cell_removed_vendor_completing_removal' => $this->input->post("fuel_cell_removed_vendor_completing_removal"),
			'fuel_cell_existing_fuel_cell_status' => $this->input->post("fuel_cell_existing_fuel_cell_status"),
			'fuel_cell_existing__fuel_cell_manufacturer' => $this->input->post("fuel_cell_existing_fuel_cell_manufacturer"),
			'fuel_cell_existing_fuel_cell_model' => $this->input->post("fuel_cell_existing_fuel_cell_model"),
			'fuel_cell_existing_fuel_cell_cabinet_serial_number' => $this->input->post("fuel_cell_existing_fuel_cell_cabinet_serial_number"),
			'fuel_cell_existing_gas_cylinder_cabinet_serial_number' => $this->input->post("fuel_cell_existing_gas_cylinder_cabinet_serial_number"),
			'fuel_cell_existing_location_discription' => $this->input->post("fuel_cell_existing_location_description"),
			'fuel_cell_comments' => $this->input->post("fuel_cell_comments"),
			'other_hazardous_material_other_hazmat_add_removed_or_existing' => $this->input->post("other_hazarous_other_hazmat_added_removed_or_existing"),
			'other_hazardous_material_hazardous_material' => $this->input->post("other_hazarous_hazardous_material"),
			'other_hazardous_material_container_location' => $this->input->post("other_hazarous_location_description"),
			'other_hazardous_material_container_type' => $this->input->post("other_hazarous_container_type"),
			'other_hazardous_material_container_size' => $this->input->post("other_hazarous_container_size"),
			'other_hazardous_material_number_of_containers' => $this->input->post("number_of_container"),
			'other_hazardous_material_inventory_delivery_date' => $this->input->post("other_hazarous_inventory_delivery"),
			'other_hazardous_material_inventory_installation_date' => $this->input->post("other_hazarous_inventory_installation_date"),
			'other_hazardous_material_inventory_removal_date' => $this->input->post("other_hazarous_inventory_removal_date"),
			'other_hazardous_material_does_this_site_have_freon' => $this->input->post("refrigerants_does_this_site_have_freon"),
			'other_hazardous_material_if_so_how_much_total_freon' => $this->input->post("refrigerants_if_so_how_much_total_freon_is_at_the_location"),
			'other_hazardous_material_other_hazmat_comments' => $this->input->post("refrigerants_other_hazmat_comments"),
			'certification_name' => $this->input->post("certification_name"),
			'certification_at_and_t_project_manager' => $this->input->post("certification_at_and_t_manager"),
			'certification_company_vendor_responsible_for_work' => $this->input->post("certification_company_vendor"),
			'certification_telephone_number_and_or_email' => $this->input->post("certification_telephone_number_and_or_email"),
			'certification_date' => $this->input->post("certification_date")

		);
		if (isset($_FILES['generator_battery_photo_1'])) {
			$generator_battery_photo_1 = $this->_upload_site_image($_FILES['generator_battery_photo_1']);
			$data['generator_battery_photo_1'] = $generator_battery_photo_1;
		} else {
			$generator_battery_photo_1 = '';
		}
		if (isset($_FILES['generator_battery_photo_2'])) {
			$generator_battery_photo_2 = $this->_upload_site_image($_FILES['generator_battery_photo_2']);
			$data['generator_battery_photo_2'] = $generator_battery_photo_2;
		} else {
			$generator_battery_photo_2 = '';
		}
		if (isset($_FILES['generator_battery_photo_3'])) {
			$generator_battery_photo_3 = $this->_upload_site_image($_FILES['generator_battery_photo_3']);
			$data['generator_battery_photo_3'] = $generator_battery_photo_3;
		} else {
			$generator_battery_photo_3 = '';
		}
		if (isset($_FILES['generator_battery_photo_4'])) {
			$generator_battery_photo_4 = $this->_upload_site_image($_FILES['generator_battery_photo_4']);
			$data['generator_battery_photo_4'] = $generator_battery_photo_4;
		} else {
			$generator_battery_photo_4 = '';
		}
		if (isset($_FILES['generator_battery_photo_5'])) {
			$generator_battery_photo_5 = $this->_upload_site_image($_FILES['generator_battery_photo_5']);
			$data['generator_battery_photo_5'] = $generator_battery_photo_5;
		} else {
			$generator_battery_photo_5 = '';
		}
		if (isset($_FILES['generator_battery_photo_6'])) {
			$generator_battery_photo_6 = $this->_upload_site_image($_FILES['generator_battery_photo_6']);
			$data['generator_battery_photo_6'] = $generator_battery_photo_6;
		} else {
			$generator_battery_photo_6 = '';
		}
		if (isset($_FILES['generator_battery_photo_7'])) {
			$generator_battery_photo_7 = $this->_upload_site_image($_FILES['generator_battery_photo_7']);
			$data['generator_battery_photo_7'] = $generator_battery_photo_7;
		} else {
			$generator_battery_photo_7 = '';
		}
		if (isset($_FILES['generator_battery_photo_8'])) {
			$generator_battery_photo_8 = $this->_upload_site_image($_FILES['generator_battery_photo_8']);
			$data['generator_battery_photo_8'] = $generator_battery_photo_8;
		} else {
			$generator_battery_photo_8 = '';
		}


		if (isset($_FILES['add_battery_photo_1'])) {
			$battery_photo_1 = $this->_upload_site_image($_FILES['add_battery_photo_1']);
			$data['battery_photo_1'] = $battery_photo_1;
		} else {
			$battery_photo_1 = '';
		}
		if (isset($_FILES['add_battery_photo_2'])) {
			$battery_photo_2 = $this->_upload_site_image($_FILES['add_battery_photo_2']);
			$data['battery_photo_2'] = $battery_photo_2;
		} else {
			$battery_photo_2 = '';
		}
		if (isset($_FILES['add_battery_photo_3'])) {
			$battery_photo_3 = $this->_upload_site_image($_FILES['add_battery_photo_3']);
			$data['battery_photo_3'] = $battery_photo_3;
		} else {
			$battery_photo_3 = '';
		}
		if (isset($_FILES['add_battery_photo_4'])) {
			$battery_photo_4 = $this->_upload_site_image($_FILES['add_battery_photo_4']);
			$data['battery_photo_4'] = $battery_photo_4;
		} else {
			$battery_photo_4 = '';
		}
		if (isset($_FILES['add_battery_photo_5'])) {
			$battery_photo_5 = $this->_upload_site_image($_FILES['add_battery_photo_5']);
			$data['battery_photo_5'] = $battery_photo_5;
		} else {
			$battery_photo_5 = '';
		}
		if (isset($_FILES['add_battery_photo_6'])) {
			$battery_photo_6 = $this->_upload_site_image($_FILES['add_battery_photo_6']);
			$data['battery_photo_6'] = $battery_photo_6;
		} else {
			$battery_photo_6 = '';
		}
		if (isset($_FILES['add_battery_photo_7'])) {
			$battery_photo_7 = $this->_upload_site_image($_FILES['add_battery_photo_7']);
			$data['battery_photo_7'] = $battery_photo_7;
		} else {
			$battery_photo_7 = '';
		}
		if (isset($_FILES['add_battery_photo_8'])) {
			$battery_photo_8 = $this->_upload_site_image($_FILES['add_battery_photo_8']);
			$data['battery_photo_8'] = $battery_photo_8;
		} else {
			$battery_photo_8 = '';
		}
		if (isset($_FILES['add_battery_photo_9'])) {
			$battery_photo_9 = $this->_upload_site_image($_FILES['add_battery_photo_9']);
			$data['battery_photo_9'] = $battery_photo_9;
		} else {
			$battery_photo_9 = '';
		}
		if (isset($_FILES['add_battery_photo_10'])) {
			$battery_photo_10 = $this->_upload_site_image($_FILES['add_battery_photo_10']);
			$data['battery_photo_10'] = $battery_photo_10;
		} else {
			$battery_photo_10 = '';
		}

		if (isset($_FILES['tank_photo_1'])) {
			$tank_photo_1 = $this->_upload_site_image($_FILES['tank_photo_1']);
			$data['fuel_tank_photo_1'] = $tank_photo_1;
		} else {
			$tank_photo_1 = '';
		}
		if (isset($_FILES['tank_photo_2'])) {
			$tank_photo_2 = $this->_upload_site_image($_FILES['tank_photo_2']);
			$data['fuel_tank_photo_2'] = $tank_photo_2;
		} else {
			$tank_photo_2 = '';
		}
		if (isset($_FILES['tank_photo_3'])) {
			$tank_photo_3 = $this->_upload_site_image($_FILES['tank_photo_3']);
			$data['fuel_tank_photo_3'] = $tank_photo_3;
		} else {
			$tank_photo_3 = '';
		}
		if (isset($_FILES['tank_photo_4'])) {
			$tank_photo_4 = $this->_upload_site_image($_FILES['tank_photo_4']);
			$data['fuel_tank_photo_4'] = $tank_photo_4;
		} else {
			$tank_photo_4 = '';
		}
		if (isset($_FILES['tank_photo_5'])) {
			$tank_photo_5 = $this->_upload_site_image($_FILES['tank_photo_5']);
			$data['fuel_tank_photo_5'] = $tank_photo_5;
		} else {
			$tank_photo_5 = '';
		}
		if (isset($_FILES['tank_photo_6'])) {
			$tank_photo_6 = $this->_upload_site_image($_FILES['tank_photo_6']);
			$data['fuel_tank_photo_6'] = $tank_photo_6;
		} else {
			$tank_photo_6 = '';
		}



		if (isset($_FILES['generator_and_engine_photo_1'])) {
			$generator_and_engine_photo_1 = $this->_upload_site_image($_FILES['generator_and_engine_photo_1']);
			$data['generator_and_engine_photo_1'] = $generator_and_engine_photo_1;
		} else {
			$generator_and_engine_photo_1 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_2'])) {
			$generator_and_engine_photo_2 = $this->_upload_site_image($_FILES['generator_and_engine_photo_2']);
			$data['generator_and_engine_photo_2'] = $generator_and_engine_photo_2;
		} else {
			$generator_and_engine_photo_2 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_3'])) {
			$generator_and_engine_photo_3 = $this->_upload_site_image($_FILES['generator_and_engine_photo_3']);
			$data['generator_and_engine_photo_3'] = $generator_and_engine_photo_3;
		} else {
			$generator_and_engine_photo_3 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_4'])) {
			$generator_and_engine_photo_4 = $this->_upload_site_image($_FILES['generator_and_engine_photo_4']);
			$data['generator_and_engine_photo_4'] = $generator_and_engine_photo_4;
		} else {
			$generator_and_engine_photo_4 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_5'])) {
			$generator_and_engine_photo_5 = $this->_upload_site_image($_FILES['generator_and_engine_photo_5']);
			$data['generator_and_engine_photo_5'] = $generator_and_engine_photo_5;
		} else {
			$generator_and_engine_photo_5 = '';
		}
		if (isset($_FILES['generator_and_engine_photo_6'])) {
			$generator_and_engine_photo_6 = $this->_upload_site_image($_FILES['generator_and_engine_photo_6']);
			$data['generator_and_engine_photo_6'] = $generator_and_engine_photo_6;
		} else {
			$generator_and_engine_photo_6 = '';
		}

		if (isset($_FILES['cylinder_photo_1'])) {
			$cylinder_photo_1 = $this->_upload_site_image($_FILES['cylinder_photo_1']);
			$data['cylinder_photo_1'] = $cylinder_photo_1;
		} else {
			$cylinder_photo_1 = '';
		}
		if (isset($_FILES['cylinder_photo_2'])) {
			$cylinder_photo_2 = $this->_upload_site_image($_FILES['cylinder_photo_2']);
			$data['cylinder_photo_2'] = $cylinder_photo_2;
		} else {
			$cylinder_photo_2 = '';
		}
		if (isset($_FILES['cylinder_photo_3'])) {
			$cylinder_photo_3 = $this->_upload_site_image($_FILES['cylinder_photo_3']);
			$data['cylinder_photo_3'] = $cylinder_photo_3;
		} else {
			$cylinder_photo_3 = '';
		}
		if (isset($_FILES['cylinder_photo_4'])) {
			$cylinder_photo_4 = $this->_upload_site_image($_FILES['cylinder_photo_4']);
			$data['cylinder_photo_4'] = $cylinder_photo_4;
		} else {
			$cylinder_photo_4 = '';
		}
		if (isset($_FILES['cylinder_photo_5'])) {
			$cylinder_photo_5 = $this->_upload_site_image($_FILES['cylinder_photo_5']);
			$data['cylinder_photo_5'] = $cylinder_photo_5;
		} else {
			$cylinder_photo_5 = '';
		}
		if (isset($_FILES['cylinder_photo_6'])) {
			$cylinder_photo_6 = $this->_upload_site_image($_FILES['cylinder_photo_6']);
			$data['cylinder_photo_6'] = $cylinder_photo_6;
		} else {
			$cylinder_photo_6 = '';
		}
		if (isset($_FILES['cylinder_photo_7'])) {
			$cylinder_photo_7 = $this->_upload_site_image($_FILES['cylinder_photo_7']);
			$data['cylinder_photo_7'] = $cylinder_photo_7;
		} else {
			$cylinder_photo_7 = '';
		}
		if (isset($_FILES['cylinder_photo_8'])) {
			$cylinder_photo_8 = $this->_upload_site_image($_FILES['cylinder_photo_8']);
			$data['cylinder_photo_8'] = $cylinder_photo_8;
		} else {
			$cylinder_photo_8 = '';
		}

		if (isset($_FILES['fueLcell_photo_1'])) {
			$fuel_cell_photo_1 = $this->_upload_site_image($_FILES['fueLcell_photo_1']);
			$data['fuel_cell_photo_1'] = $fuel_cell_photo_1;
		} else {
			$fuel_cell_photo_1 = '';
		}
		if (isset($_FILES['fueLcell_photo_2'])) {
			$fuel_cell_photo_2 = $this->_upload_site_image($_FILES['fueLcell_photo_2']);
			$data['fuel_cell_photo_2'] = $fuel_cell_photo_2;
		} else {
			$fuel_cell_photo_2 = '';
		}
		if (isset($_FILES['fueLcell_photo_3'])) {
			$fuel_cell_photo_3 = $this->_upload_site_image($_FILES['fueLcell_photo_3']);
			$data['fuel_cell_photo_3'] = $fuel_cell_photo_3;
		} else {
			$fuel_cell_photo_3 = '';
		}
		if (isset($_FILES['fueLcell_photo_4'])) {
			$fuel_cell_photo_4 = $this->_upload_site_image($_FILES['fueLcell_photo_4']);
			$data['fuel_cell_photo_4'] = $fuel_cell_photo_4;
		} else {
			$fuel_cell_photo_4 = '';
		}
		if (isset($_FILES['fueLcell_photo_5'])) {
			$fuel_cell_photo_5 = $this->_upload_site_image($_FILES['fueLcell_photo_5']);
			$data['fuel_cell_photo_5'] = $fuel_cell_photo_5;
		} else {
			$fuel_cell_photo_5 = '';
		}
		if (isset($_FILES['fueLcell_photo_6'])) {
			$fuel_cell_photo_6 = $this->_upload_site_image($_FILES['fueLcell_photo_6']);
			$data['fuel_cell_photo_6'] = $fuel_cell_photo_6;
		} else {
			$fuel_cell_photo_6 = '';
		}
		if (isset($_FILES['fueLcell_photo_7'])) {
			$fuel_cell_photo_7 = $this->_upload_site_image($_FILES['fueLcell_photo_7']);
			$data['fuel_cell_photo_7'] = $fuel_cell_photo_7;
		} else {
			$fuel_cell_photo_7 = '';
		}
		if (isset($_FILES['fueLcell_photo_8'])) {
			$fuel_cell_photo_8 = $this->_upload_site_image($_FILES['fueLcell_photo_8']);
			$data['fuel_cell_photo_8'] = $fuel_cell_photo_8;
		} else {
			$fuel_cell_photo_8 = '';
		}
		if (isset($_FILES['other_hazardous_material_photo_1'])) {
			$other_hazardous_material_photo_1 = $this->_upload_site_image($_FILES['other_hazardous_material_photo_1']);
			$data['required_hazardous_material_photo_1'] = $other_hazardous_material_photo_1;
		} else {
			$other_hazardous_material_photo_1 = '';
		}
		if (isset($_FILES['other_hazardous_material_photo_2'])) {
			$other_hazardous_material_photo_2 = $this->_upload_site_image($_FILES['other_hazardous_material_photo_2']);
			$data['required_hazardous_material_photo_2'] = $other_hazardous_material_photo_2;
		} else {
			$other_hazardous_material_photo_2 = '';
		}
		if (isset($_POST['certificate_signature']) && $_POST['certificate_signature'] != '') {
			time();
			$certificate_signature =  rand() . '.jpg';
			$filePath = './uploads/' . $certificate_signature;
			$file = fopen($filePath, 'w');
			fwrite($file, base64_decode($_POST['certificate_signature']));
			fclose($file);
			$data['certification_signature'] = $certificate_signature;
		} else {
			$certificate_signature = '';
		}

		$update = $this->users->update_sites($site_id, $data);
		$this->_site_pdf($site_id);
	}
	function _upload_site_image($file = null)
	{

		$this->load->library('session');
		if (isset($file) && $file != null) {
			time();
			$extension = explode('.', $file['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './uploads/' . $new_name;
			move_uploaded_file($file['tmp_name'], $destination);
			return $new_name;
		} else {
			return '';
		}
	}

	public function add_disposal()
	{
		$this->load->model("users");
		if (isset($_POST['signature']) && $_POST['signature'] != '') {
			time();
			$signature =  rand() . '.jpg';
			$filePath = './uploads/' . $signature;
			$file = fopen($filePath, 'w');
			fwrite($file, base64_decode($_POST['signature']));
			fclose($file);
		} else {
			$signature = '';
		}

		$data = array(
			'todays_date' => $this->input->post("todays_date"),
			'callers_name' => $this->input->post("callers_name"),
			'caller_office_number' => $this->input->post("caller_office_number"),
			'caller_other_number' => $this->input->post("caller_other_number"),
			'address' => $this->input->post("address"),
			'city' => $this->input->post("city"),
			'state' => $this->input->post("state"),
			'zip' => $this->input->post("zip"),
			'usid' => $this->input->post("usid"),
			'fa_location_code' => $this->input->post("fa_location_code"),
			'longitude' => $this->input->post("longitude"),

			'latitude' => $this->input->post("latitude"),
			'requested_date' => $this->input->post("requested_date"),
			'available_hours' => $this->input->post("available_hours"),
			'normal_working_hours' => $this->input->post("normal_working_hours"),
			'special_site_access_requirements' => $this->input->post("special_site_access_requirements"),
			'access_road_to_facility' => $this->input->post("access_road_to_facility"),
			'special_access_requirements' => $this->input->post("special_access_requirements"),
			'is_the_site_ground_level' => $this->input->post("is_the_site_ground_level"),
			'do_the_batteries_contain_metal_sleeves' => $this->input->post("do_the_batteries_contain_metal_sleeves"),
			'primary_medium' => $this->input->post("primary_medium"),

			'special_battery_handling' => $this->input->post("special_battery_handling"),
			'special_floor_protection_needs' => $this->input->post("special_floor_protection_needs"),
			'any_special_building_scuirity_issues' => $this->input->post("any_special_building_scuirity_issues"),
			'any_hieght_or_length' => $this->input->post("any_hieght_or_length"),
			'battery_type1' => $this->input->post("battery_type1"),
			'qty1' => $this->input->post("qty1"),
			'weight_per_battery1' => $this->input->post("weight_per_battery1"),
			'make1' => $this->input->post("make1"),
			'model_numbers1' => $this->input->post("model_numbers1"),
			'battery_type2' => $this->input->post("battery_type2"),

			'qty2' => $this->input->post("qty2"),
			'weight_per_battery2' => $this->input->post("weight_per_battery2"),
			'make2' => $this->input->post("make2"),
			'model_numbers2' => $this->input->post("model_numbers2"),
			'battery_type3' => $this->input->post("battery_type3"),
			'qty3' => $this->input->post("qty3"),
			'weight_per_battery3' => $this->input->post("weight_per_battery3"),
			'make3' => $this->input->post("make3"),
			'model_numbers3' => $this->input->post("model_numbers3"),
			'battery_type4' => $this->input->post("battery_type4"),

			'qty4' => $this->input->post("qty4"),
			'weight_per_battery4' => $this->input->post("weight_per_battery4"),
			'make4' => $this->input->post("make4"),
			'model_numbers4' => $this->input->post("model_numbers4"),
			'other_special' => $this->input->post("other_special"),
			'name' => $this->input->post("name"),
			'date' => $this->input->post("date"),
			'signature' => $signature
		);
		$insert = $this->users->addDisposal($data);
		$disposal_id = $this->users->lastID();
		$this->request_form_pdf($disposal_id);
	}

	public function get_disposal()
	{
		$this->load->library('session');
		$this->load->model("users");
		$diposal_data['data'] = $this->users->getDisposal();
		echo json_encode($diposal_data);
	}

	public function get_disposal_to_show()
	{
		$this->load->library('session');
		$this->load->model("users");
		$diposal_data = $this->users->getDisposalToShow($_POST['disposal_id']);
		echo json_encode($diposal_data);
	}

	public function update_disposal()
	{
		$this->load->model("users");
		$disposal_id = $_POST['disposal_id'];
		$data = array(
			'todays_date' => $this->input->post("todays_date"),
			'callers_name' => $this->input->post("callers_name"),
			'caller_office_number' => $this->input->post("caller_office_number"),
			'caller_other_number' => $this->input->post("caller_other_number"),
			'address' => $this->input->post("address"),
			'city' => $this->input->post("city"),
			'state' => $this->input->post("state"),
			'zip' => $this->input->post("zip"),
			'usid' => $this->input->post("usid"),
			'fa_location_code' => $this->input->post("fa_location_code"),
			'longitude' => $this->input->post("longitude"),

			'latitude' => $this->input->post("latitude"),
			'requested_date' => $this->input->post("requested_date"),
			'available_hours' => $this->input->post("available_hours"),
			'normal_working_hours' => $this->input->post("normal_working_hours"),
			'special_site_access_requirements' => $this->input->post("special_site_access_requirements"),
			'access_road_to_facility' => $this->input->post("access_road_to_facility"),
			'special_access_requirements' => $this->input->post("special_access_requirements"),
			'is_the_site_ground_level' => $this->input->post("is_the_site_ground_level"),
			'do_the_batteries_contain_metal_sleeves' => $this->input->post("do_the_batteries_contain_metal_sleeves"),
			'primary_medium' => $this->input->post("primary_medium"),

			'special_battery_handling' => $this->input->post("special_battery_handling"),
			'special_floor_protection_needs' => $this->input->post("special_floor_protection_needs"),
			'any_special_building_scuirity_issues' => $this->input->post("any_special_building_scuirity_issues"),
			'any_hieght_or_length' => $this->input->post("any_hieght_or_length"),
			'battery_type1' => $this->input->post("battery_type1"),
			'qty1' => $this->input->post("qty1"),
			'weight_per_battery1' => $this->input->post("weight_per_battery1"),
			'make1' => $this->input->post("make1"),
			'model_numbers1' => $this->input->post("model_numbers1"),
			'battery_type2' => $this->input->post("battery_type2"),

			'qty2' => $this->input->post("qty2"),
			'weight_per_battery2' => $this->input->post("weight_per_battery2"),
			'make2' => $this->input->post("make2"),
			'model_numbers2' => $this->input->post("model_numbers2"),
			'battery_type3' => $this->input->post("battery_type3"),
			'qty3' => $this->input->post("qty3"),
			'weight_per_battery3' => $this->input->post("weight_per_battery3"),
			'make3' => $this->input->post("make3"),
			'model_numbers3' => $this->input->post("model_numbers3"),
			'battery_type4' => $this->input->post("battery_type4"),

			'qty4' => $this->input->post("qty4"),
			'weight_per_battery4' => $this->input->post("weight_per_battery4"),
			'make4' => $this->input->post("make4"),
			'model_numbers4' => $this->input->post("model_numbers4"),
			'other_special' => $this->input->post("other_special"),
			'name' => $this->input->post("name"),
			'date' => $this->input->post("date")
			// 'signature' => $signature
		);
		if (isset($_POST['signature']) && $_POST['signature'] != '') {
			time();
			$signature =  rand() . '.jpg';
			$filePath = './uploads/' . $signature;
			$file = fopen($filePath, 'w');
			fwrite($file, base64_decode($_POST['signature']));
			fclose($file);
			$data['signature'] = $signature;
		}
		$update = $this->users->updateDisposal($disposal_id, $data);
		$this->request_form_pdf($disposal_id);
	}

	public function delete_selected_disposal()
	{
		if (isset($_POST['disposal_id'])) {
			$this->load->model('users');
			$this->users->deleteDisposal($_POST['disposal_id']);
		}
	}

	function _site_pdf($site_id = null)
	{
		$this->load->model('site_model');
		$site = $this->site_model->getSitesForPdf($site_id);
		// print_r($site);

		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Site PDF');
		// $pdf->SetHeaderMargin(20);
		// $pdf->SetTopMargin(10);
		// $pdf->setFooterMargin(10);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->SetMargins(11, 10, 15, true);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->AddPage();
		$html = '';
		$pdf->Image('/telco/uploads/sample/att_logo.jpg', 'L', 6, '30', '15', 'JPG', false, 'L', false, 300, 'L', false, false, 0, false, false, false);
		$pdf->SetXY(0, 50);
		$pdf->SetXY(10, 20);
		// $pdf->writeHTML('', true, false, true, false, '');
		$pdf->SetXY(10, 5);
		$hazardous_material = $site->hazardous_material_changes_reported;
		$hazardous_material_data = '';
		if (strpos($hazardous_material, 'Battery') !== false) {
			echo '<input type="checkbox" name="check1" id="check1" value = "checked"  checked="checked" readonly="true">Batteries &nbsp;';
		}
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<thead>
<tr >
<th colspan="6" align="right" height="47px" width="542px"><p style="font-size: 7px; color: red;"><br>National EH&S Hazardous Materials Inventory Form<br>Version: 3<br>Last Modified: 8/23/18</p></th>
</tr>
 <tr >
 <th colspan="6" align="center" height="40px" width="542px" style="font-size: 10px;"><strong>National Hazardous Materials Inventory Form</strong><br>All EH&S inventories must be reported and recorded within 5 business days of change<br><label style=" color: red;">(INCORRECT OR INCOMPLETE FORMS AS WELL AS FORMS NOT CERTIFIED WILL BE RETURNED)</label></th>
 </tr>
 <tr >
 <th colspan="6" align="center" height="32px" width="542px" style="font-size: 10px;">Submit Hazardous Materials Inventory Form to the following:<br><label style=" color: red;">(All forms must be titled and include the email subject of EH305_Region_Market_FA Location Code_USID_Project)</label></th>

</thead>
</tr>
<tr style="line-height: 20px; font-size: 9px;">
<td align="left" width="200px">Name: $site->site_name</td>
<td align="left" width="100px">ATTUID: $site->attuid</td>
<td align="left" width="242px">Email Address: $site->email</td>
</tr>
<tr style="line-height: 20px; font-size: 9px;">
<td align="left" width="200px">Group Mailbox/Email Address: $site->group_mailbox</td>
<td align="left" width="100px">Region: $site->region</td>
<td align="right" width="121px">Corporate EH&S: $site->corporate</td>
<td align="left" width="121px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="225px">Facility/Site Information</td>
<td align="center" width="100px"><label style=" color: red;">Example</label></td>
<td align="center" width="217px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Region</td>
<td align="center" width="100px"><label style=" color: red;">WEST</label></td>
<td align="center" width="217px">$site->facility_region</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Market</td>
<td align="center" width="100px"><label style=" color: red;">RMR (CO/ID/MT/NE/UT/WY)</label></td>
<td align="center" width="217px">$site->market</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site ID & Name</td>
<td align="center" width="100px"><label style=" color: red;">NEU3804 Alliance</label></td>
<td align="center" width="217px">$site->site_id_and_name</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >USID</td>
<td align="center" width="100px"><label style=" color: red;">150669</label></td>
<td align="center" width="217px">$site->usid</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >FA Location Code</td>
<td align="center" width="100px"><label style=" color: red;">12854291</label></td>
<td align="center" width="217px">$site->fa_location_code</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Oracle Project Tracking Number</td>
<td align="center" width="100px"><label style=" color: red;">3769673998</label></td>
<td align="center" width="217px">$site->oracle_Project_tracking_number</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >PACE Project Tracking Number</td>
<td align="center" width="100px"><label style=" color: red;">MRUTH012362</label></td>
<td align="center" width="217px">$site->pace_project_tracking_number</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >AT&T Construction Manager</td>
<td align="center" width="100px"><label style=" color: red;">Jane Smith</label></td>
<td align="center" width="217px">$site->at_and_t_construction_manager</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Street Address</td>
<td align="center" width="100px"><label style=" color: red;">2503 COUNTY RD.</label></td>
<td align="center" width="217px">$site->strett_address</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >City</td>
<td align="center" width="100px"><label style=" color: red;">ALIANCE</label></td>
<td align="center" width="217px">$site->city</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >County</td>
<td align="center" width="100px"><label style=" color: red;">BOX BUTTE</label></td>
<td align="center" width="217px">$site->country</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >State</td>
<td align="center" width="100px"><label style=" color: red;">NE</label></td>
<td align="center" width="217px">$site->state</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Zip Code</td>
<td align="center" width="100px"><label style=" color: red;">69301</label></td>
<td align="center" width="217px">$site->zip_code</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site Type</td>
<td align="center" width="100px"><label style=" color: red;">Shelter</label></td>
<td align="center" width="217px">$site->site_type</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site Located On</td>
<td align="center" width="100px"><label style=" color: red;">Private Property - Leased</label></td>
<td align="center" width="217px">$site->site_located_on</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Project Type</td>
<td align="center" width="100px"><label style=" color: red;">Site Overlay.LTE NSB</label></td>
<td align="center" width="217px">$site->project_type</td>
</tr>
<tr style="line-height: 10px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; " ><p>Hazardous Material Changes reported on this form:  Please Check all that apply</p></td>
<td align="center" width="100px"><p style=" color: red; "><input type="checkbox" name="check1" id="check1" value = "checked"  checked="checked" readonly="true">Batteries</p></td>
<td align="center" width="217px">

EOD;
		$hazardous_material_data = "";
		$hazardous_material = $site->hazardous_material_changes_reported;
		if (strpos($hazardous_material, 'Batteries') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked"  checked="checked" readonly="true">Batteries &nbsp;
EOD;
		}

		if (strpos($hazardous_material, 'Tank') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked"  checked="checked" readonly="true">Tank &nbsp;
EOD;
		}

		if (strpos($hazardous_material, 'Generator') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked" checked="checked" readonly="true">Generator &nbsp;
EOD;
		}

		if (strpos($hazardous_material, 'Start Up Battery') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked" checked="checked" readonly="true">Start Up Baterry <br>
EOD;
		}

		if (strpos($hazardous_material, 'Cylinder') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked" checked="checked" readonly="true">Cylinder &nbsp;
EOD;
		}

		if (strpos($hazardous_material, 'Other') !==  false) {
$tbl .= <<<EOD
            <input type="checkbox" name="check1" id="check1" value = "checked" checked="checked" readonly="true">Other -Explaine Below &nbsp;
EOD;
		}

$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px"  ><u><i>Other Hazardous Materials</i></u> <br><br><label>Enter any other comments that might pertain to other hazardous materials changes</label></td>
<td align="center" width="317px">$site->other_hazardous_material</td>
</tr>
<tr style="line-height: 5px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
</tr>
<tr style="line-height: 15px; font-size: 12px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Information</b></td>
</tr>


<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="202.2px" >Battery Information</td>
<td align="center" width="70px"><label style=" color: red;"><u>Example</u></label></td>
<td align="center" width="60px"><u>Battery Added</u></td>
<td align="center" width="70px"><u>Battery Added (2)</u></td>
<td align="center" width="70px"><u>Battery Added (3)</u></td>
<td align="center" width="70px"><u>Battery Added (4)</u></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery manufacturer</td>
<td align="center" width="70px"><label style=" color: red;">DEKA</label></td>
<td align="center" width="60px">$site->battery_manufcaturer</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery model</td>
<td align="center" width="70px"><label style=" color: red;">Liberty 2000</label></td>
<td align="center" width="60px">$site->batterY_model</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">HD-1100</label></td>
<td align="center" width="60px">$site->battery_list_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery manufacturer date</td>
<td align="center" width="70px"><label style=" color: red;">10/8/2017</label></td>
<td align="center" width="60px">$site->battery_manufacturer_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Added Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">8</label></td>
<td align="center" width="60px">$site->battery_removed_quantity</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="60px">$site->battery_status</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="70px"><label style=" color: red;">Floor 4, Room 410, Northwest corner</label></td>
<td align="center" width="60px">$site->battery_location_discription</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px">$site->cabinet_type</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">2</label></td>
<td align="center" width="60px">$site->cabinet_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery <label style=" color: red;">DELIVERY</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="60px">$site->battery_delivery_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 2px solid #fff;">Battery <label style=" color: red;">INSTALLATION</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="60px">$site->battery_installation_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Vendor Completing Installation</td>
<td align="center" width="70px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="60px">$site->vendor_completing_installation</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" ><u>Removed</u></td>
<td align="center" width="70px"><label style=" color: red;"><u>Battery Removed</u></label></td>
<td align="center" width="60px"><u>Battery Removed</u></td>
<td align="center" width="70px"><u>Battery Removed (2)</u></td>
<td align="center" width="70px"><u>Battery Removed (3)</u></td>
<td align="center" width="70px"><u>Battery Removed (4)</u></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Manufacturer</td>
<td align="center" width="70px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="60px">$site->removed_battery_manufacturer</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Model</td>
<td align="center" width="70px"><label style=" color: red;">NSB100FT</label></td>
<td align="center" width="60px">$site->removed_battery_model</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="60px">$site->removed_battery_list_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Manufacturer Date</td>
<td align="center" width="70px"><label style=" color: red;">10/10/2010</label></td>
<td align="center" width="60px">$site->removed_battery_manufacturer_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1.5px solid #fff;">Added Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">24</label></td>
<td align="center" width="60px">$site->removed_battery_removed_quantity</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style=" border-top: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Inactive - Removed</label></td>
<td align="center" width="60px">$site->removed_battery_status</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, room, and floor as well as the direction</td>
<td align="center" width="70px"><label style=" color: red;">Floor 4, Room 410, Northwest corner</label></td>
<td align="center" width="60px">$site->removed_battery_location_discription</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px">$site->removed_cabinet_type</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">2</label></td>
<td align="center" width="60px">$site->removed_cabinet_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery <label style=" color: red;">REMOVAL</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">7/31/2018</label></td>
<td align="center" width="60px">$site->removed_battery_removal_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style=" border-top: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="70px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="60px">$site->removed_vendor_completing_removal</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" ><u>Existing (not added or removed as part of the project)</u></td>
<td align="center" width="70px"><label style=" color: red;"><u>Existing Battery</u></label></td>
<td align="center" width="60px"><u>Existing Battery</u></td>
<td align="center" width="70px"><u>Existing Battery (2)</u></td>
<td align="center" width="70px"><u>Existing Battery (3)</u></td>
<td align="center" width="70px"><u>Existing Battery (4)</u></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery manufacturer</td>
<td align="center" width="70px"><label style=" color: red;">GNB</label></td>
<td align="center" width="60px">$site->existing_battery_manufacturer</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Model</td>
<td align="center" width="70px"><label style=" color: red;">MARATHON</label></td>
<td align="center" width="60px">$site->existing_battery_modal</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">M12V155FT</label></td>
<td align="center" width="60px">$site->existing_battery_list_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Manufacturer Date</td>
<td align="center" width="70px"><label style=" color: red;">11/14/2013</label></td>
<td align="center" width="60px">$site->existing_battery_manufacturer_date</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Existing Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">12</label></td>
<td align="center" width="60px">$site->existing_battery_quantity</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="60px">$site->existing_battery_status</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="70px"><label style=" color: red;">Floor 4, Room 410, Northwest corner</label></td>
<td align="center" width="60px">$site->existing_battery_location_discription</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px">$site->existing_cabinet_type</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">1</label></td>
<td align="center" width="60px">$site->existing_cabinet_number</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="font-size: 7px;">Total number of battery units on-site before installation and/or removal work</td>
<td align="center" width="130px">$site->total_number_of_battery_units_on_site_before_installation</td>
<td align="center" width="210px"  style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="font-size: 7px;">Total number of battery units on-site after installation and/or removal work</td>
<td align="center" width="130px">$site->total_number_of_battery_units_on_site_after_installation</td>
<td align="center" width="210px"  style="border-top: 1px solid #fff;"></td>
</tr>
<tr style="line-height: 15px; font-size: 12px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Information - California Proposition 65 Signage</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Is the Proposition 65 sign posted for the presence of lead-acid batteries <label style=" color: red;">(California Only)</label>?</td>
<td align="center" width="70px">$site->is_the_position_65_sign_posted_for_the_presence_of_lead_acid</td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u><b>Battery Comments</b></u><br><label>Enter any other comments that might pertain to these batteries or the California Proposition 65 Signage</label></i></td>
<td align="center" width="340px">$site->battery_comments</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING BATTERY PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the battery manufacturer, model, and list number</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b>2</b> - Photo(s) clearly showing the total number of battery "Units"</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b>3</b>- Photo(s) clearly showing the location of the battery cabinet or rack within the equipment location as follows:</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b><u>FOR INDOOR SITES:</u></b> Photo(s) showing the location of the battery cabinet/rack for added and existing batteries in relation to the equipment room/shelter entry/doorway</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b><u>FOR OUTDOOR SITES: </u></b> Photo(s) showing the location of the battery cabinet/rack for added and existing batteries in relation to the compound entry/gateway and/or the antenna support structure/tower</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px">  <b> 4 </b>- Photo(s) showing the location of the Proposition 65 sign (California Only)</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px" style=" font-size: 10px;"><label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
EOD;

		// $pdf->Image('/telco/uploads/sample/att_logo.jpg', 'L', 6, '30', '15', 'JPG', false, 'L', false, 300, 'L', false, false, 0, false, false, false);

$tbl .= <<<EOD
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Photos - Added Batteries</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the added batteries including:  Battery Manufacture - Model - List Number</td>
<td align="center" width="130px">Photo(s) clearly showing the total Number of batteries added</td>
<td align="center" width="140px">Photo(s) clearly showing the location of the battery cabinet or rack within the equipment/site location</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="272.2px" >
EOD;
		if ($site->battery_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_1" height= "130" width="272.2">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">

EOD;
		if ($site->battery_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_2" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
        </td>
<td align="center" width="140px">
EOD;
		if ($site->battery_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_3" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>

</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Photos - Removed Batteries</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) of the removed batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">
EOD;
		if ($site->battery_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_4" height= "130" width="332px">
EOD;
		}
$tbl .= <<<EOD
        </td>
<td align="center" width="210px">
EOD;
		if ($site->battery_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_5" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Photos - Existing Batteries</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of existing batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="130px">Photo(s) clearly showing the total number of existing batteries</td>
<td align="center" width="140px">Photo(s) clearly showing the location of the battery cabinet or rack within the equipment/site location</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->battery_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_6" height= "130" width="272px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->battery_photo_7 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_7" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->battery_photo_8 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_8" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Proposition 65 Sign Photos <label style=" color: red;">(California Only)</label></b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) clearly showing the proposition 65 sign on the cabinet, shelter door, site access door, or compound fence</td>
<td align="center" width="210px">Photo(s) clearly showing the proposition 65 sign for the site in relation to the entrance </td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">
EOD;
		if ($site->battery_photo_9 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_9" height= "130" width="332px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->battery_photo_10 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->battery_photo_10" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
</tr>
<tr style="line-height: 5px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
</tr>

</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Information</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="225px">Generator Start-up Battery Information</td>
<td align="center" width="100px"><label style=" color: red;"><b><i><u>Example</u></i></label></b></td>
<td align="center" width="77px"><b><i><u>Battery Added</u></i></b></td>
<td align="center" width="140px"><b><i><u>Battery Added</u></i></b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery manufacturer</td>
<td align="center" width="100px"><label style=" color: red;">Optima</label></td>
<td align="center" width="77px">$site->generator_battery_manufacturer</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">Red Top</label></td>
<td align="center" width="77px">$site->generator_battery_model</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">SC75U</label></td>
<td align="center" width="77px">$site->generator_battery_list_number</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Added Quantity (Individual batteries/units)</td>
<td align="center" width="100px"><label style=" color: red;">1</label></td>
<td align="center" width="77px">$site->generator_added_quantity</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">Active</label></td>
<td align="center" width="77px">$site->generator_battery_status</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound in generator enclosure</label></td>
<td align="center" width="77px">$site->generator_location_discription</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery <label style=" color: red;">DELIVERY</label> Date</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px">$site->generator_battery_delivery_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery <label style=" color: red;">INSTALLATION</label> Date</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px">$site->generator_battery_installation_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Installation</td>
<td align="center" width="100px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="77px">$site->generator_vendor_completing_installation</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><b><i><u>Removed</u></i></b></td>
<td align="center" width="100px"><label style=" color: red;"><b><i><u>Battery Removed</u></i></b></label></td>
<td align="center" width="77px"><b><i><u>Battery Removed</u></i></b></td>
<td align="center" width="140px"><b><i><u>Battery Removed</u></i></b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery manufacturer</td>
<td align="center" width="100px"><label style=" color: red;">Optima</label></td>
<td align="center" width="77px">$site->generator_removed_battery_manufacturer</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">Red Top</label></td>
<td align="center" width="77px">$site->generator_removed_battery_model</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">SC75U</label></td>
<td align="center" width="77px">$site->generator_removed_list_number</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Removed Quantity <label style=" font-size: 7px;">(Individual batteries)</label></td>
<td align="center" width="100px"><label style=" color: red;">1</label></td>
<td align="center" width="77px">$site->generator_removed_quantity</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">Inactive - Removed</label></td>
<td align="center" width="77px">$site->generator_removed_battery_status</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound in generator enclosure</label></td>
<td align="center" width="77px">$site->generator_removed_location_discription</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Date battery <label style=" color: red;">REMOVED</label> from site</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px">$site->generator_removed_date_battery_from_site</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Removal</td>
<td align="center" width="100px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="77px">$site->generator_removed_vendor_completing_removal</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><b><i><u>Existing (not added or removed as part of the project)</u></i></b></td>
<td align="center" width="100px"><label style=" color: red;"><b><i><u>Existing Battery</u></i></b></label></td>
<td align="center" width="77px"><b><i><u>Existing Battery</u></i></b></td>
<td align="center" width="140px"><b><i><u>Existing Battery</u></i></b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery manufacturer</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->genarator_existing_battery_manufacturer</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->generator_existing_battery_model</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->generator_existing_battery_list_number</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Existing Quantity <label style=" font-size: 7px;">(Individual batteries)</label></td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->generator_existing_existing_quanitity</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->generator_existing_battery_status</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->generator_existing_location_discription</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><b><i><u>Battery Comments</u></i></b><br><label>Enter any other comments that might pertain to these batteries</label></td>
<td align="center" width="0.1px"><label style=" color: red;"></label></td>
<td align="center" width="317px">$site->generator_existing_battery_comments</td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING BATTERY PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the battery manufacturer, model, and list number</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the total number of battery "Units"</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) showing the location of the added and existing batteries in relation to the generator/engine</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px" style=" font-size: 9px;"><label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Photos - Added Batteries</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="130px">Photo(s) clearly showing the total number of  batteries added</td>
<td align="center" width="140px">Photo(s) clearly showing the location of  added batteries</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->generator_battery_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_1" height= "130" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->generator_battery_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_2" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->generator_battery_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_3" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
        </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Photos - Removed Batteries</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) of the removed batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">
EOD;
		if ($site->generator_battery_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_4" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->generator_battery_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_5" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Photos - Existing Batteries</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="130px">Photo(s) clearly showing the total number of  existing batteries</td>
<td align="center" width="140px">Photo(s) clearly showing the location of  existing batteries</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->generator_battery_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_6" height= "130" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->generator_battery_photo_7 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_7" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->generator_battery_photo_8 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_battery_photo_8" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Tank Information</b></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><label style=" color: red;"><i>90 DAYS PRIOR TO INSTALLATION, MODIFICATION, AND/OR REMOVAL, THE AT&T EH&S TANK TEAM MUST BE NOTIFIED OF THE PROJECT FOR APPLICABLE PERMITTING OR SPILL PREVENTION REQUIREMENTS </i></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="225px">Fuel Tank Information</td>
<td align="center" width="100px"><label style=" color: red;"><b><i><u>Example</u></i></label></b></td>
<td align="center" width="77px"><b><i><u>Fuel Tank</u></i></b></td>
<td align="center" width="140px"><b><i><u>Fuel Tank (2)</u></i></b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Added, Removed, or Existing</td>
<td align="center" width="100px"><label style=" color: red;">Added</label></td>
<td align="center" width="77px">$site->fuel_tank_added_Removed_or_existing</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Status</td>
<td align="center" width="100px"><label style=" color: red;">Active</label></td>
<td align="center" width="77px">$site->fuel_tank_status</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Type</td>
<td align="center" width="100px"><label style=" color: red;">Aboveground</label></td>
<td align="center" width="77px">$site->fuel_tank_type</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Portable or Fixed</td>
<td align="center" width="100px"><label style=" color: red;">Fixed</label></td>
<td align="center" width="77px">$site->fuel_tank_portable_fixed</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Type of Fuel </td>
<td align="center" width="100px"><label style=" color: red;">Ultra Low Sulfur Diesel</label></td>
<td align="center" width="77px">$site->fuel_tank_type_of_fuel</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Total Storage Capacity of Tank (gallons)</td>
<td align="center" width="100px"><label style=" color: red;">210</label></td>
<td align="center" width="77px">$site->fuel_tank_total_storage_capacity_of_tank_gallons</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Description</td>
<td align="center" width="100px"><label style=" color: red;">Main Tank</label></td>
<td align="center" width="77px">$site->fuel_tank_disciption</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Manufacturer</td>
<td align="center" width="100px"><label style=" color: red;">American Welding & Tank</label></td>
<td align="center" width="77px">$site->fuel_tank_manufacturer</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Serial Number</td>
<td align="center" width="100px"><label style=" color: red;">55R1012A</label></td>
<td align="center" width="77px">$site->fuel_tank_serial_number</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Manufacture Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_tank_manufacturer_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Delivery Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_delivery_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Install Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_install_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Installation</td>
<td align="center" width="100px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="77px">$site->fuel_tank_vendor_completing_installation</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Usage</td>
<td align="center" width="100px"><label style=" color: red;">Emergency Power</label></td>
<td align="center" width="77px">$site->fuel_tank_usage</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Usage Status</td>
<td align="center" width="100px"><label style=" color: red;">Currently In use</label></td>
<td align="center" width="77px">$site->fuel_tank_usage_status</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Is the tank indoor or outdoor?</td>
<td align="center" width="100px"><label style=" color: red;">Outdoor</label></td>
<td align="center" width="77px">$site->fuel_tank_indoor_or_outdoor</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound </label></td>
<td align="center" width="77px">$site->fuel_tank_location_discription</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Structure</td>
<td align="center" width="100px"><label style=" color: red;">Double Walled</label></td>
<td align="center" width="77px">$site->fuel_tank_structure</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Exterior Wall Structure</td>
<td align="center" width="100px"><label style=" color: red;">Bare Steel</label></td>
<td align="center" width="77px">$site->fuel_tank_exterior_wall_structure</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Ownership</td>
<td align="center" width="100px"><label style=" color: red;">Owned</label></td>
<td align="center" width="77px">$site->fuel_tank_ownership</td>
<td align="center" width="140px"></td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Owner Name</td>
<td align="center" width="100px"><label style=" color: red;">AT&T</label></td>
<td align="center" width="77px">$site->fuel_tank_owner_name</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">First Fuel Date</td>
<td align="center" width="100px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_first_fuel_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Removal Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_removal_date</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Removal</td>
<td align="center" width="100px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="77px">$site->fuel_tank_vendor_completing_removal</td>
<td align="center" width="140px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Tank Installation Projects</u></i></td>
<td align="center" width="317px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><label>Was approval received from EH&S through a Notice to Proceed (NTP) issued by Trinity Consultants or EBI Consulting prior to delivery/installation? </label></td>
<td align="center" width="100px"><label style=" color: red;">Yes</label></td>
<td align="center" width="77px">$site->fuel_tank_was_approved_recieved</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Date of NTP Approval</td>
<td align="center" width="100px"><label style=" color: red;">7/31/2017</label></td>
<td align="center" width="77px">$site->fuel_tank_date_of_ntp_approval</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Is the tank permitted?<br><label style=" color: red;">(NTP AND PERMITS MUST BE UPLOADED TO CCN/FILENET UNDER CONTENT/DOCUMENT ID EH309)</label></td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px">$site->fuel_tank_is_the_task_permitted</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Type</td>
<td align="center" width="100px"><label style=" color: red;">Exempt</label></td>
<td align="center" width="77px">$site->fuel_tank_Permit_type</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permitting Agency (If Applicable)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->fuel_tank_permitting_agency</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Number (If Applicable)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->fuel_tank_permit_number</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Expiration (Month/Year)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->fuel_tank_permit_expiration</td>
<td align="center" width="140px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Does the tank have the following?</u></i></td>
<td align="center" width="317px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Spill Kit (Required by local jurisdiction or for spill prevention plan)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px">$site->fuel_tank_spill_kit</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Spill Bucket</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px">$site->fuel_tank_spill_bucket</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Secondary Containment</td>
<td align="center" width="100px"><label style=" color: red;">None</label></td>
<td align="center" width="77px">$site->fuel_tank_secondary_containment</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Leak Detection</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px">$site->fuel_tank_leak_detection</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Overfill Protection</td>
<td align="center" width="100px"><label style=" color: red;">Auto Shutoff</label></td>
<td align="center" width="77px">$site->fuel_tank_overfill_protection</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Cathodic Protection Present</td>
<td align="center" width="100px"><label style=" color: red;">None</label></td>
<td align="center" width="77px">$site->fuel_tank_cathodic_protection_present</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Safety Post/Traffic Bollard Protection</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px">$site->fuel_tank__safety_post</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Proper labeling/marking for contents (i.e. Diesel, Propane, etc.)</td>
<td align="center" width="100px"><label style=" color: red;">Yes</label></td>
<td align="center" width="77px">$site->fuel_tank_proper_labeling</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tertiary Containment</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px">$site->fuel_tank_tertiary_containment</td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Tank Comments</u></i><br><br><label>Enter any other comments that might pertain to these tanks</label></td>
<td align="center" width="317px">$site->fuel_tank_comments</td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING TANK PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the plate(s) on the tank (information plate on the tank indicates manufacturer, model, serial number, capacity/size, manufacture date, etc.)</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the label(s) on the tank, identifying the contents, fire hazard, and AT&T EH&S Hotline</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) clearly showing the location of the tank within the compound entry/gateway and/or the antenna support structure/tower (If separate from the generator) </td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>4</b> - Photo(s) clearly showing the tank piping, venting, and spill kit with location, if applicable</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Tank Photos</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Close up photo(s) of the tank information plate(s) including:  Manufacturer - Model - Serial Number - Capacity/Size - Manufacture Date - Etc.</td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->fuel_tank_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_1" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->fuel_tank_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_2" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="210px"></td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the location of the tank </td>
<td align="center" width="210px">Photo(s) of the tank piping </td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->fuel_tank_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_3" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->fuel_tank_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_4" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) of the tank venting</td>
<td align="center" width="210px">Photo(s) clearly showing the spill kit and location</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->fuel_tank_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_5" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">

EOD;
		if ($site->fuel_tank_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_tank_photo_6" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
        </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 5px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator and Engine Information</b></td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><label style=" color: red;">90 DAYS PRIOR TO INSTALLATION, MODIFICATION, AND/OR REMOVAL, THE AT&T EH&S AIR TEAM MUST BE NOTIFIED OF THE PROJECT FOR PERMITTING APPLICABLE REQUIREMENTS </label></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ><b>Generator Information</b></td>
<td align="center" width="70px"><label style=" color: red;"><u><i>Example</label></i></u></td>
<td align="center" width="70px"><u><i>Generator</i></u></td>
<td align="center" width="70px"><u><i>Generator (2)</i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Added, Removed, or Existing</td>
<td align="center" width="70px"><label style=" color: red;">Added</label></td>
<td align="center" width="70px">$site->generator_added_removed_or_existing</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Status</td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="70px">$site->generator_status</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Type</td>
<td align="center" width="70px"><label style=" color: red;">Internal Combustion</label></td>
<td align="center" width="70px">$site->generator_type</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Manufacturer</td>
<td align="center" width="70px"><label style=" color: red;">Kohler</label></td>
<td align="center" width="70px">$site->generator_manufacturer</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Model</td>
<td align="center" width="70px"><label style=" color: red;">50REOZJE</label></td>
<td align="center" width="70px">$site->generator_model</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Serial Number</td>
<td align="center" width="70px"><label style=" color: red;">SGM32JM6S</label></td>
<td align="center" width="70px">$site->generator_serial_number</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Output Rating Value (in kW)</td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px">$site->generator_output_rating_value</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Portable or Fixed</td>
<td align="center" width="70px"><label style=" color: red;">Fixed</label></td>
<td align="center" width="70px">$site->generator_portable_or_fixed</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="70px"><label style=" color: red;">Southwest corner of fenced compound </label></td>
<td align="center" width="70px">$site->generator_location_description</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Delivery Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px">$site->generator_delivery_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Installation Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px">$site->generator_installation_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Manufacture Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="70px">$site->generator_manufacturer_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Ownership</td>
<td align="center" width="70px"><label style=" color: red;">Owned</label></td>
<td align="center" width="70px">$site->generator_ownership</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="70px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="70px">$site->generator_and_engine_vendor_completing_installation</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Removal Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">12/31//2017</label></td>
<td align="center" width="70px">$site->generator_removal_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-top: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="70px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="70px">$site->generator_vendor_completing_removal</td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u><b>Cell Site Power and Generator Operation</b></u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is Generator Prime power?</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->generator_is_generator_prime_power</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is commercial power used at site?</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->generator_is_commercial_power_used_at_site</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Non-Resettable Hour Meter?</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->generator_non_resettable_hour_meter</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Current Hour Meter Reading?</td>
<td align="center" width="70px"><label style=" color: red;">5</label></td>
<td align="center" width="70px">$site->generator_current_hour_meter_reading</td>
<td align="center" width="70px"></td>
</tr>
</table>

EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date of Hour Meter Reading?</td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px">$site->generator_date_hour_meter_reading</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff; font-size: 7px;">Date of first start-up/first fire</td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px">$site->generator_date_of_first_start_up_first_fire</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="  font-size: 7px;">Is there a generator on site used for emergency power not belonging to AT&T Mobility?</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->generator_is_there_a_generator_on_site_used_for_emergency_power</td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u>Generator Comments</u></i><br><label  style="font-size: 7px;">Enter any other comments that might pertain to these generators</label></td>
<td align="center" width="340px" style="font-size: 12px;">$site->generator_comments</td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ><b>Engine Information</b></td>
<td align="center" width="70px"><label style=" color: red;"><u><i>Example</label></i></u></td>
<td align="center" width="70px"><u><i>Engine</i></u></td>
<td align="center" width="70px"><u><i>Engine (2)</i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Manufacturer</td>
<td align="center" width="70px"><label style=" color: red;">John Deere</label></td>
<td align="center" width="70px">$site->engine_manufacturer</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Model</td>
<td align="center" width="70px"><label style=" color: red;">4045TF280</label></td>
<td align="center" width="70px">$site->engine_model</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Serial Number</td>
<td align="center" width="70px"><label style=" color: red;">PE4045N003528</label></td>
<td align="center" width="70px">$site->engine_serial_number</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Type</td>
<td align="center" width="70px"><label style=" color: red;">Diesel Outside</label></td>
<td align="center" width="70px">$site->engine_type</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Type of Fuel</td>
<td align="center" width="70px"><label style=" color: red;">Ultra Low Sulfur Diesel</label></td>
<td align="center" width="70px">$site->engine_type_of_fuel</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Manufacture Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="70px">$site->engine_manufacturer_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Install Date (MM/DD/YYYY)</td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px">$site->engine_install_date</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Cylinders</td>
<td align="center" width="70px"><label style=" color: red;">4</label></td>
<td align="center" width="70px">$site->engine_number_of_cylinders</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Max Engine kilowatts (kW)</td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px">$site->engine_max_engine_kilowatts</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Rated Horsepower (HP)</td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px">$site->engine_rated_horsepower</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Max Brake Horsepower (BHP)</td>
<td align="center" width="70px"><label style=" color: red;">85</label></td>
<td align="center" width="70px">$site->engine_max_brake_horsepower</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Displacement (Liters) or (Cubic Inches)</td>
<td align="center" width="70px"><label style=" color: red;">4.5 Liters</label></td>
<td align="center" width="70px">$site->engine_displacement_liters_or_cubic_inches</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">EPA Family Number</td>
<td align="center" width="70px"><label style=" color: red;">HJDXL04.5141</label></td>
<td align="center" width="70px">$site->engine_epa_family_number</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Diameter of stack outlet (inches)</td>
<td align="center" width="70px"><label style=" color: red;">3</label></td>
<td align="center" width="70px">$site->engine_diameter_of_stack_outlet_inches</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Exhaust stack height from ground (feet)</td>
<td align="center" width="70px"><label style=" color: red;">8</label></td>
<td align="center" width="70px">$site->engine__exhaust_stack_height_from_ground</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Direction of exhaust outlet (Horizontal or Vertical)</td>
<td align="center" width="70px"><label style=" color: red;">Vertical</label></td>
<td align="center" width="70px">$site->engine_direction_of_exhouse_outlet</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">End of stack (Open or Capped)</td>
<td align="center" width="70px"><label style=" color: red;">Open</label></td>
<td align="center" width="70px">$site->engine_end_of_stack</td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Generator Installation Projects</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Was approval received from EH&S through a Notice to Proceed (NTP) issued by Trinity Consultants or EBI Consulting prior to delivery/installation? </td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->generator_was_approval_recieved_from_eh_and_s</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date of NTP Approval</td>
<td align="center" width="70px"><label style=" color: red;">07/31/2017</label></td>
<td align="center" width="70px">$site->generator_date_of_ntp_approval</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is the generator/engine permitted?<br><label style="color: red;">(NTP AND PERMITS MUST BE UPLOADED TO CCN/FILENET UNDER CONTENT/DOCUMENT ID EH309)</label></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->generator_is_the_generator_or_engine_permitted</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Type</td>
<td align="center" width="70px"><label style=" color: red;">Exempt</label></td>
<td align="center" width="70px">$site->generator_permit_type</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permitting Agency (If Applicable)</td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px">$site->generator_permitting_agency</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Number (If Applicable)</td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px">$site->generator_permit_number</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Expiration (Month/Year)</td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px">$site->generator_permit_expiration</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Generator Distance (<label style=" color: red;">California Only</label>)</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest fence line (feet)</td>
<td align="center" width="70px"><label style=" color: red;">10</label></td>
<td align="center" width="70px">$site->generator_distance_from_engine_to_nearest_fence_line</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest residence (feet)</td>
<td align="center" width="70px"><label style=" color: red;">500</label></td>
<td align="center" width="70px">$site->generator_distance_from_engine_to_nearest_residence</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest business (feet)</td>
<td align="center" width="70px"><label style=" color: red;">1000</label></td>
<td align="center" width="70px">$site->generator_distance_from_engine_to_nearest_business</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Is engine located within 500 feet of a school, daycare, hospital, or care facility?</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->generator_is_engine_located_within_500_feet_of_from_a_school</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If yes, name of facility</td>
<td align="center" width="70px"><label style=" color: red;"></label></td>
<td align="center" width="70px">$site->generator_if_yes_name_of_facility</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If yes, distance</td>
<td align="center" width="70px"><label style=" color: red;"></label></td>
<td align="center" width="70px">$site->generator_if_yes_distance</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Where applicable, does the engine have the following?</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Direct Injection</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->engine_direct_injection</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Diesel Particulate Filter</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->engine_diesel_particular_filter</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Injection Time Retard</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->engine_injection_time_retard</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Other Emission Controls</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->engine_other_emission_controls</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Turbo Charger</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->engine_turbo_charger</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Diesel oxidation catalyst</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->engine_diesel_oxidation_catalyst</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Catalytic Converter</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->engine_catalytic_converter</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Intercooler</td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px">$site->engine_intercooler</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Aftercooler</td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px">$site->engine_aftercooler</td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u>Engine Comments</u></i><br><label  style="font-size: 7px;">Enter any other comments that might pertain to these generators</label></td>
<td align="center" width="340px">$site->engine_comments</td>
</tr>



</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING GENERATOR AND ENGINE PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up Photo(s) clearly showing the plates(s) on (1) the Generator and (2) Engine (Information plate on both the Generator and Engine lists EPA Information, manufacturer, model, serial number, manufacture date, etc.)</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the location of the generator within the compound entry/gateway</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) clearly showing the left and right side of the engine compartment</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>4</b> - Photo(s) clearly showing the exhaust stack and hour meter with the current reading</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>5</b> - Photo or generator/engine specification document clearly showing the engine Horsepower (HP) and Brake Horsepower (BHP)</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="542px"><b>Generator and Engine Photos</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Close up photo(s) of the generator information plate(s) including:  Manufacturer - Model - Serial Number - Size - Manufacture Date - Etc.</td>
<td align="center" width="210px">Close up photo(s) of the engine information plate(s) including:  Manufacturer - Model - Serial Number - Size - Manufacture Date - Etc.</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->generator_and_engine_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_1" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->generator_and_engine_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_2" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the location of the generator</td>
<td align="center" width="210px">Photo(s) of the left and right side of the engine compartment</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->generator_and_engine_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_3" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->generator_and_engine_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_4" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) of the exhaust stack</td>
<td align="center" width="210px">Photoof the hour meter with the current reading</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->generator_and_engine_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_5" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->generator_and_engine_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->generator_and_engine_photo_6" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="center" width="542px">Generator/engine specification document to identify the engine Horsepower (HP) and Brake Horsepower (BHP)</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 11px; ">
<td align="center" width="542px"><b></b></td>
</tr>

</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="542px"><b>Cylinder Information</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;"><b>Cylinder Information</b></td>
<td align="center" width="96px"><label style=" color: red;"><i><b><u>Example</u></b></i></label></td>
<td align="center" width="64px"><i><b><u>Cylinders Added</b></u></i></td>
<td align="center" width="78px"><i><b><u>Cylinders Added (2)</u></b></i></td>
<td align="center" width="99px"><i><b><u>Cylinders Added (3)</u></b></i></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px">$site->cylinder_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>HYDROGEN</i></label></td>
<td align="center" width="64px">$site->cylinder_contents</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Cylinder(s) Installed</td>
<td align="center" width="96px"><label style=" color: red;"><i>16</i></label></td>
<td align="center" width="64px">$site->cylinder_number_of_cylinder_installed</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>4176 ft3</i></label></td>
<td align="center" width="64px">$site->cylinder_total_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>261 ft3</i></label></td>
<td align="center" width="64px">$site->cylinder_largiest_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">DELIVERED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->cylinder_date_cylinder_delivered</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">INSTALLED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->cylinder_date_cylinder_installed</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East corner of shelter</i></label></td>
<td align="center" width="64px">$site->cylinder_location_of_cylinders</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px">$site->cylinder_vendor_completing_installation</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Removed</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Cylinders Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Cylinders Removed</b></i></u></td>
<td align="center" width="78px"><u><i><b>Cylinders Removed (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Cylinders Removed (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Inactive - Removed</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_cylinder_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>HYDROGEN</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_cylinder_contents</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>1566 ft3</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_total_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>261 ft3</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_largest_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Removed Quantity (Individual cylinders/units)</td>
<td align="center" width="96px"><label style=" color: red;"><i>6</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_quantity</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">REMOVED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_date_cylinders_removed</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East corner of shelter</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_location_of_cylinders</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px">$site->cylinder_removed_vendor_completing_removal</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Existing (not added or removed as part of the project)</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Existing Cylinders (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Existing Cylinders</b></i></u></td>
<td align="center" width="78px"><u><i><b>Existing Cylinders (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Existing Cylinders (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_cylinder_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_cylinder_contents</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_total_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_largest_cylinder_volume</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Existing Quantity (Individual cylinders/units)</td>
<td align="center" width="96px"><label style=" color: red;"><i>0</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_quantiity</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->cylinder_existing_location_of_cylinder</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total number of Cylinder units on-site before installation and/or removal work</td>
<td align="center" width="96px" ><label style=" color: red;"><i>6</i></label></td>
<td align="center" width="64px">$site->cylinder_total_number_of_cylinder_units_on_site</td>
<td align="center" width="177px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Total number of Cylinder units on-site after installation and/or removal work</td>
<td align="center" width="96px" ><label style=" color: red;"><i>16</i></label></td>
<td align="center" width="64px">$site->cylinder_total_number_cylinder_units_on_site_after_installation</td>
<td align="center" width="177px" style="border-top-color: white; border-bottom: 1px solid #010000;"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Cylinder Comments</b></u><br><label style="font-size: 7px;">Enter any other comments that might pertain to these cylinders:</label></td>
<td align="center" width="337" >$site->cylinder_comments</td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING CYLINDER PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the information label on the cylinder cabinet/rack</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) clearly showing the number of cylinders and the cylinder location</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Photos - Added Cylinders</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the information label on the cylinder cabinet/rack</td>
<td align="center" width="130px">Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="140px">Photo(s) clearly showing the number of cylinders and the cylinder location</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->cylinder_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_1" height= "130" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->cylinder_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_2" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->cylinder_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_3" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>

</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Photos - Removed Cylinders</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="210px">Photo(s) clearly showing the number of cylinders removed</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->cylinder_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_4" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->cylinder_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_5" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="542px"><b>Cylinder Photos - Existing Cylinders</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Close-up photo(s) clearly showing the information label on the cabinet/rack</td>
<td align="center" width="130px">Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="140px">Photo(s) clearly showing the number of cylinders and the cylinder location</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->cylinder_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_6" height= "130" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->cylinder_photo_7 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_7" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->cylinder_photo_8 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->cylinder_photo_8" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Information</b></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="205px" ><u><i><b>Fuel Cell Information</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Example</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Fuel Cell Added</b></i></u></td>
<td align="center" width="78px"><u><i><b>Fuel Cell Added (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Fuel Cell Added (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px">$site->fuel_cell_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>ReliOn-PLUG Power</i></label></td>
<td align="center" width="64px">$site->fuel_cell_manufacturer</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Model</td>
<td align="center" width="96px"><label style=" color: red;"><i>T-2000</i></label></td>
<td align="center" width="64px">$site->fuel_cell_model</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet Serial Number</td>
<td align="center" width="96px"><label style=" color: red;"><i>REL10060006</i></label></td>
<td align="center" width="64px">$site->fuel_cell_cabinet_serial_number</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">DELIVERED</label> to site</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->fuel_cell_date_fuel_cell_delivery_to_site</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">INSTALLED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->fuel_cell_date_fuel_cell_installed</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>West side of compound in cabinet</i></label></td>
<td align="center" width="64px">$site->fuel_cell_location_discription</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px">$site->fuel_cell_vendor_completing_installation</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Removed</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Fuel Cell Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Fuel Cell Removed</b></i></u></td>
<td align="center" width="78px"><u><i><b>Fuel Cell Removed (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Fuel Cell Removed (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Inactive - Removed</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_fuel_cell_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>Relion</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_fuel_cell_manufacturer</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Model</td>
<td align="center" width="96px"><label style=" color: red;"><i>T-2000</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_fuel_cell_model</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>REL10060006</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_cabinet_serial_number</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">REMOVED</label> from site</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_date_fuel_cell_remove_from_site</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>West side of compound in cabinet</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_location_discription</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px">$site->fuel_cell_removed_vendor_completing_removal</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Existing (not added or removed as part of the project)</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Fuel Cell Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Existing Fuel Cell</b></i></u></td>
<td align="center" width="78px"><u><i><b>Existing Fuel Cell (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Existing Fuel Cell (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing_fuel_cell_status</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing__fuel_cell_manufacturer</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell model</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing_fuel_cell_model</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing_fuel_cell_cabinet_serial_number</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Gas Cylinder Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing_gas_cylinder_cabinet_serial_number</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->fuel_cell_existing_location_discription</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Fuel Cell Comments</b></u><br><label style="font-size: 7px;">Enter any other comments that might pertain to these fuel cells</label></td>
<td align="center" width="337" ><label style=" color: red;">$site->fuel_cell_existing_location_discription</label></td>
</tr>


<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING FUEL CELL PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the fuel cell label including manufacturer/model</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the fuel cell cabinet location</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) clearly showing the fuel cell cabinet and chassis serial numbers</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Added Fuel Cells</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the fuel cell label including manufacturer/model</td>
<td align="center" width="130px">Photo(s) clearly showing the fuel cell cabinet location</td>
<td align="center" width="140px">Photo(s) clearly showing the fuel cell cabinet and chassis serial numbers</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->fuel_cell_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_1" height= "130" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->fuel_cell_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_2" height= "130" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->fuel_cell_photo_3 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_3" height= "130" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Removed Fuel Cells</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the removed fuel cell label including manufacturer/model</td>
<td align="center" width="210px">Photo(s) clearly showing the removed fuel cell cabinet and chassis serial numbers</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->fuel_cell_photo_4 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_4" height= "130" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->fuel_cell_photo_5 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_5" height= "130" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Existing Fuel Cells</b></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the existing fuel cell label including manufacturer/model</td>
<td align="center" width="130px">Photo(s) clearly showing the existing fuel cell cabinet location</td>
<td align="center" width="140px">Photo(s) clearly showing the existing fuel cell cabinet and chassis serial numbers</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" >
EOD;
		if ($site->fuel_cell_photo_6 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_6" height= "120" width="272.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
		if ($site->fuel_cell_photo_7 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_7" height= "120" width="130px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
		if ($site->fuel_cell_photo_8 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->fuel_cell_photo_8" height= "120" width="140px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="130px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="center" width="542px"><b></b></td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Other Hazardous Materials Information</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="205px" ><u><i><b>Other HazMat Information</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Example</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>HazMat</b></i></u></td>
<td align="center" width="78px"><u><i><b>HazMat (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>HazMat (3)</b></i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Other HazMat Added, Removed, or Existing</td>
<td align="center" width="96px"><label style=" color: red;"><i>Added</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_other_hazmat_add_removed_or_existing</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Hazardous Material</td>
<td align="center" width="96px"><label style=" color: red;"><i>Hazardous Material</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_hazardous_material</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Location  (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East side of compound</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_container_location</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Type</td>
<td align="center" width="96px"><label style=" color: red;"><i>Drum</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_container_type</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Size</td>
<td align="center" width="96px"><label style=" color: red;"><i>55 gallons</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_container_size</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Containers</td>
<td align="center" width="96px"><label style=" color: red;"><i>1</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_number_of_containers</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Delivery Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_inventory_delivery_date</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Installation Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_inventory_installation_date</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Removal Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px">$site->other_hazardous_material_inventory_removal_date</td>
<td align="center" width="78px"></td>
<td align="center" width="99px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Refrigerants</b></u></td>
<td align="center" width="337" ><label style=" color: red;"></label></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Does this site have Freon?</td>
<td align="center" width="96px"><label style=" color: red;"><i>No</i></label></td>
<td align="center" width="241px">$site->other_hazardous_material_does_this_site_have_freon</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If so, how much total Freon is at the location (lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="241px">$site->other_hazardous_material_if_so_how_much_total_freon</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Other HazMat Comments</b></u></td>
<td align="center" width="337" >$site->other_hazardous_material_other_hazmat_comments</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING FUEL CELL PHOTOS ARE REQUIRED :</u></b></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the information label on the hazardous material container</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the number of hazardous materials containers and the container locations</td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="542px"><b>Hazardous Materials Photos - Existing Containers</b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the information label on the hazardous material container</td>
<td align="center" width="210px">Photo(s) clearly showing the number of hazardous materials containers and the container locations</td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" >
EOD;
		if ($site->required_hazardous_material_photo_1 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->required_hazardous_material_photo_1" height= "120" width="332.2px">
EOD;
		}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
		if ($site->required_hazardous_material_photo_2 !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->required_hazardous_material_photo_2" height= "120" width="210px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="210px"> </td>
</tr>
<tr style="line-height: 15px; font-size: 11px; background-color: yellow;">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b><label style=" color: red; ">Certification (Required for all inventory changes)</label></b></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Name (Please Print)</b></td>
<td align="left" width="337" >$site->certification_name</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Signature</b></td>
<td align="left" width="337" >
EOD;
		if ($site->certification_signature !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$site->certification_signature" height= "50" width="70px">
EOD;
		}
$tbl .= <<<EOD
</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>AT&T Project Manager</b></td>
<td align="left" width="337" >$site->certification_at_and_t_project_manager</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Company/Vendor Responsible for Work</b></td>
<td align="left" width="337" >$site->certification_company_vendor_responsible_for_work</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Telephone Number and/or Email </b></td>
<td align="left" width="337" >$site->certification_telephone_number_and_or_email</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Date</b></td>
<td align="left" width="337" >$site->certification_date</td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		ob_end_clean();
		$filename = "site_pdf_" . $site_id . ".pdf";
		$path = "./uploads/";
		$pdf->Output($path . $filename, 'F');
	}

	public function request_form_pdf($disposal_id = null)
	{
		$this->load->model('users');
		$disposal = $this->users->getDisposalForPdf($disposal_id);
		// print_r($disposal);
		// echo "asdasdas";
		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Disposal Request Form PDF');
		// $pdf->SetHeaderMargin(20);
		// $pdf->SetTopMargin(10);
		// $pdf->setFooterMargin(10);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->SetMargins(10, 10, 10, true);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->AddPage();
		$html = '';
		$pdf->SetXY(0, 50);
		$pdf->Image('/telco/uploads/sample/att_logo.jpg', 'C', 11.5, '21', '10.5', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
		$pdf->SetXY(10, 20);
		// $pdf->writeHTML('', true, false, true, false, '');
		$pdf->SetXY(10, 7);
$tbl = <<<EOD
	<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<thead>
<tr >
<th colspan="6" align="left" height="45px" width="50%" style="border-color: white; border: 2px solid #fff;"><p style="font-size: 8px;"><br>AT&T Environment, Health & Safety<br>Document Owner: Director – EH&S Technical Support<br>Document Author: Cindi Cave, cc1898</p></th>
<th colspan="6" align="right" height="45px" width="50%" style="border-color: white; border: 2px solid #fff;"><p style="font-size: 8px;"><br>Form #: EHS-400-FRM-10D<br>Issue Date: 11/23/2012<br>Version: 1.1</p><br></th>
</tr>
<tr><th colspan="6" align="center" height="8px" width="100%" style="border-bottom-color: black; border-left-color: white; border-bottom: 1px solid #fff; border-left: 2px solid #fff; border-right: 2px solid #fff;"><b>MOBILITY BATTERY DISPOSAL REQUEST FORM</b></th></tr>
</thead>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="100%"><b>Mobility Used Battery Disposal Request<br>(Provide this information whether contacting by phone or email)</b></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Today’s Date:</b> $disposal->todays_date</td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Callers Name:</b> $disposal->callers_name</td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Caller Phone Number(s):</b>&nbsp;&nbsp;&nbsp;&nbsp;Office: $disposal->caller_office_number<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other: $disposal->caller_other_number<br></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Location:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Address: $disposal->address<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;City: $disposal->city<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State: $disposal->state &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="margin-right: 150px; text-align: right;">Zip: $disposal->zip</label><br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;USID: $disposal->usid <br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FA Location Code: $disposal->fa_location_code <br>
</td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="30%" style="border-right-color: white; border-right: 1px solid #fff"><b>Longitude/Latitude (If Known):</b></td>
<td align="left" width="30%" style="border-right-color: white; border-right: 1px solid #fff">Longitude: $disposal->longitude</td>
<td align="left" width="40%">Latitude: $disposal->latitude </td>
</tr>

<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="25%" style="border-top-color: black; border-right: 1px solid #fff"><b>Requested Service Date: </b> </td>
<td align="left" width="75%" style="border-top-color: black; ">Date: $disposal->requested_date<br><br>Available Hours: $disposal->available_hours<br></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black; "><b>Normal Working Hours:</b> $disposal->normal_working_hours</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Site Access Requirements:</b> $disposal->special_site_access_requirements</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Access Road to Facility (Dirt, Gravel, Asphalt):</b> $disposal->access_road_to_facility</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Access Requirements - Obstructions in the<br>Room for Moving the Batteries Around:</b> $disposal->special_access_requirements</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Is the site ground level, have multiple floors? Is there<br>a freight elevator or rooftop removal needed?</b> $disposal->is_the_site_ground_level</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Do the batteries contain metal sleeves, modules, or<br>racking?</b> $disposal->do_the_batteries_contain_metal_sleeves</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Primary medium for communicating projects and<br>project scope of work details:</b> $disposal->primary_medium</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Battery Handling Equipment Needed (Crane):</b> $disposal->special_battery_handling</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Floor Protection Needs (Masonite Sheet):</b> $disposal->special_floor_protection_needs</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Any special building security issues?</b> $disposal->any_special_building_scuirity_issues</td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Any height or length restrictions for truck?</b> $disposal->any_hieght_or_length</td>
</tr>
<br>
<br>
<br>
<p align="center" style="font-size: 8px"><b>AT&T PROPRIETARY (Internal Use Only)</b><br>Not for use or disclosure outside the AT&T companies, except under written agreement<br>Document is not controlled in printed or electronic copy. Obtain current version from EH&S Website at <label color="blue"><u>www.ehs.att.com</u></label></p>
</table>


EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$html = '';
		$pdf->SetXY(0, 50);
		$pdf->Image('/telco/uploads/sample/att_logo.jpg', 'C', 11.5, '21', '10.5', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
		$pdf->SetXY(10, 20);
		// $pdf->writeHTML('', true, false, true, false, '');
		$pdf->SetXY(10, 7);

$tbl = <<<EOD
	<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<thead>
<tr >
<th colspan="6" align="left" height="45px" width="50%" style="border-bottom-color: black; border-top-color: white; border-left-color: white; border: 1.3px solid #fff;"><p style="font-size: 8px;"><br>AT&T Environment, Health & Safety<br>Mobility Battery Disposal Request Form<br>Document Owner: Director – EH&S Technical Support<br>Document Author: Cindi Cave, cc1898</p></th>
<th colspan="6" align="right" height="45px" width="50%" style="border-bottom-color: black; border-top-color: white; border-right-color: white; border: 1.3px solid #fff;"><p style="font-size: 8px;"><br>Form #: EHS-400-FRM-10D<br>Issue Date: 11/23/2012<br>Version: 1.1</p><br></th>
</tr>
</thead>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="center" width="100%"><b>Mobility Used Battery Disposal Request<br>(Provide this information whether contacting by phone or email)</b></td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: white; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"><b>Service/Product Requested and Estimated QTY:</b></td>


EOD;
		if ($disposal->battery_type1 != "") {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true" checked="checked" >Battery Type (Lead-acid,nickel cadmium)</td>
                        <td align="left" width="35%">Qty: $disposal->qty1<br><br>Weight Per Battery: $disposal->weight_per_battery1<br><br>Make: $disposal->make1<br><br>Model Numbers: $disposal->model_numbers1</td>
EOD;
		} else {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true" >Battery Type (Lead-acid,nickel cadmium)</td>
                        <td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
EOD;
		}
$tbl .= <<<EOD

</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: white; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
EOD;
		if ($disposal->battery_type2 != "") {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check2" id="check2" value = "checked"  readonly="true" checked="checked" >Battery Type</td>
                        <td align="left" width="35%">Qty: $disposal->qty2<br><br>Weight Per Battery: $disposal->weight_per_battery2<br><br>Make: $disposal->make2<br><br>Model Numbers: $disposal->model_numbers2</td>
EOD;
		} else {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check2" id="check2" value = "checked"  readonly="true">Battery Type</td>
                        <td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
EOD;
		}
$tbl .= <<<EOD

</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: white; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
EOD;
		if ($disposal->battery_type3 != "") {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check3" id="check3" value = "checked"  readonly="true" checked="checked" >Battery Type</td>
                        <td align="left" width="35%">Qty: $disposal->qty3<br><br>Weight Per Battery: $disposal->weight_per_battery3<br><br>Make: $disposal->make3<br><br>Model Numbers: $disposal->model_numbers3</td>
EOD;
		} else {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check3" id="check3" value = "checked"  readonly="true">Battery Type</td>
                        <td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
EOD;
		}
$tbl .= <<<EOD

</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: black; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
EOD;
		if ($disposal->battery_type4 != "") {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check4" id="check4" value = "checked"  readonly="true" checked="checked" >Battery Type</td>
                        <td align="left" width="35%">Qty: $disposal->qty4<br><br>Weight Per Battery: $disposal->weight_per_battery4<br><br>Make: $disposal->make4<br><br>Model Numbers: $disposal->model_numbers4</td>
EOD;
		} else {
$tbl .= <<<EOD
                        <td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check4" id="check4" value = "checked"  readonly="true">Battery Type</td>
                        <td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
EOD;
		}
$tbl .= <<<EOD
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="left" width="100%" ><b>Other Special Instructions:</b> $disposal->other_special<br></td>
</tr>
</table>
<p><b>C&E Mobility or Turf Vendor Representative:</b></p>
<br>
<br>
<br>
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 7px; font-size: 10px; ">
<td align="left" width="15%"  style="border-color: white; border: 2px solid #fff;"><b>Name (Printed):</b><br></td>
<td align="left" width="50%" style="border-top-color: white; ">$disposal->name<br></td>
<td align="left" width="6%" style="border-color: white; border: 2px solid #fff;"><b>Date:</b><br></td>
<td align="left" width="29%" style="border-top-color: white; border-right-color: white;">$disposal->date<br></td>
</tr>
</table>
<br>
<br>
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 7px; font-size: 10px; ">
<td align="left" width="10%"  style="border-color: white; border: 2px solid #fff;"><b>Signature:</b><br></td>
<td align="left" width="50%" style="border-top-color: white; border-right-color: white;">
EOD;
		if ($disposal->signature !== "") {
$tbl .= <<<EOD
            <img src="/telco/uploads/$disposal->signature" height= "40" width="60">
EOD;
		}
$tbl .= <<<EOD
<br></td>
</tr>
</table>
<br>
<br>
<p align="center" style="font-size: 8px"><b>AT&T PROPRIETARY (Internal Use Only)</b><br>Not for use or disclosure outside the AT&T companies, except under written agreement<br>Document is not controlled in printed or electronic copy. Obtain current version from EH&S Website at <label color="blue"><u>www.ehs.att.com</u></label></p>

EOD;


		$pdf->writeHTML($tbl, true, false, false, false, '');
		ob_end_clean();
		$filename = "disposal_pdf_" . $disposal_id . ".pdf";
		$path = "./uploads/";
		$pdf->Output($path . $filename, 'F');
	}
}	//====================controller closing
