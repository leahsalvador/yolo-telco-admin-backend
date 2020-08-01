<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Bangkok');
class Main extends CI_Controller
{

	public function index()
	{
		$this->load->view('login');
	}
	public function register()
	{
		$this->load->view('register');
	}
	public function login()
	{
		if (isset($_POST['login'])) {
			$this->load->library('session');
			$this->load->model('main_model');
			$email = $_POST['email'];
			$password = $_POST['password'];
			$user = array("email" => $email, "password" => $password);
			$res = $this->main_model->get_users($user);
			$client = $this->main_model->get_client($user);
			if ($res && !$client) {
				$_SESSION['user'] = $res;
				if ($_SESSION['user']->user_group == 101) {
					echo "elmlive";
				}
			} else if ($client && !$res) {
				$_SESSION['user'] = $client;
				// if($_SESSION['user']->user_group == 3){
				// header("Location: /client");
				echo "client";
				// }
			} else {
				echo 'error';
			}
		} else {
			echo 'error2';
		}
	}

	public function all_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		if (!isset($_SESSION['user'])) {
			header("Location: /main");
		} elseif ($_SESSION['user']->user_group != "101") {
			header("Location: /main/logout_user/101");
		}
		$id = $_SESSION['user']->user_id;
		$data['user'] = $this->main_model->get_admin($id);
		$data['users'] = $this->main_model->get_user($id);
		$this->load->view('all_login', $data);
	}

	public function admin_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 0) {
			$_SESSION['user'] = $res;
			echo 'admin';
		} else {
			echo '';
		}
	}

	public function vet_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 1) {
			$_SESSION['user'] = $res;
			echo 'vet';
		} else {
			echo '';
		}
	}

	public function receptionist_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 2) {
			$_SESSION['user'] = $res;
			echo 'receptionist';
		} else {
			echo '';
		}
	}

	public function manager_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 4) {
			$_SESSION['user'] = $res;
			echo 'manager';
		} else {
			echo '';
		}
	}

	public function supply_officer_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 5) {
			$_SESSION['user'] = $res;
			echo 'supplyofficer';
		} else {
			echo '';
		}
	}

	public function cashier_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 6) {
			$_SESSION['user'] = $res;
			echo 'cashier';
		} else {
			echo '';
		}
	}

	public function accountant_login()
	{
		$this->load->library('session');
		$this->load->model('main_model');
		$email = $_POST['email'];
		$password = $_POST['password'];
		$user = array("email" => $email, "password" => $password);
		$res = $this->main_model->get_users($user);
		if ($res && $res->user_group == 7) {
			$_SESSION['user'] = $res;
			echo 'accountant';
		} else {
			echo '';
		}
	}

	public function register_client()
	{
		$this->load->model('admin_model');
		$data = array(
			"name" => $_POST['name'],
			"birthdate" => $_POST['birthdate'],
			"age" => $_POST['age'],
			"address" => $_POST['address'],
			"city" => $_POST['city'],
			"state" => $_POST['state'],
			"zipcode" => $_POST['zipcode'],
			"country" => $_POST['country'],
			"mobile" => $_POST['mobile'],
			"email" => $_POST['email'],
			"password" => $_POST['password']
		);
		$q = $this->admin_model->register_client($data);
		if ($q) {
			echo "success";
		} else {
			echo "error";
		}
	}
	public function logout()
	{
		$this->load->library('session');
		$this->session->sess_destroy();
		header("Location: /main");
	}
	public function profile()
	{
		$this->load->library("session");
		$this->load->model("main_model");
		$id = $_SESSION['user']->user_id;
		$data['user_group'] = $_SESSION['user']->user_group;
		$data['user'] = $this->main_model->get_user($id);
		$this->load->view('user_profile', $data);
	}
	public function update_user()
	{
		$this->load->library("session");
		$this->load->model("main_model");
		$user_id = $_POST['user_id'];
		$password = $_POST['password'];
		$id = $_SESSION['user']->user_id;


		// if(isset($_POST['imageData8'])){
		$filePath = './uploads/signatures/signature' . $id . '.jpg';
		// Write $imgData into the image file
		$file = fopen($filePath, 'w');
		fwrite($file, base64_decode($_POST['imageData8']));
		fclose($file);
		// }



		$check = $this->main_model->pass_exist($user_id);
		if ($check->password == $password) {
			$user = array("name" => $_POST['name'], "address" => $_POST['address'], "email" => $_POST['email'], "phone" => $_POST['phone'], "signature" => "signature" . $id . ".jpg");
		} else {
			$user = array("name" => $_POST['name'], "address" => $_POST['address'], "email" => $_POST['email'], "phone" => $_POST['phone'], "password" => md5($password), "signature" => "signature" . $id . ".jpg");
		}
		$update = $this->main_model->update_user($user_id, $user);
		// print_r($user);
	}


	public function update_profile()
	{
		$this->load->library("session");
		$this->load->model("main_model");
		// echo $_POST['profile'];
		if (isset($_POST["image"])) {
			$data = $_POST["image"];
			$image_array_1 = explode(";", $data);
			$image_array_2 = explode(",", $image_array_1[1]);
			$data = base64_decode($image_array_2[1]);
			time();
			$imageName = rand() . '.png';
			file_put_contents('uploads/' . $imageName, $data);
			$profile = array(
				'profile' => $imageName
			);
			$this->main_model->update_profile($_SESSION['user']->user_id, $profile);
		}
	}
	function _upload_image()
	{
		if (isset($_FILES['profile'])) {
			time();
			$extension = explode('.', $_FILES['profile']['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = './uploads/' . $new_name;
			move_uploaded_file($_FILES['profile']['tmp_name'], $destination);
			return $new_name;
		}
	}
	public function logout_user($user_group = null)
	{
		$this->load->library('session');
		// $this->session->sess_destroy();
		$this->load->model('main_model');
		$main = $this->main_model->get_main_user($user_group);
		$_SESSION['user'] = $main;
		header("Location: /main/all_login/");
	}


	public function test_pdf($pet_id = null)
	{
		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Test PDF');
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
		$pdf->Image('/uploads/sample/att_logo.jpg', 'L', 6, '30', '15', 'JPG', false, 'L', false, 300, 'L', false, false, 0, false, false, false);
		$pdf->SetXY(0, 50);
		$pdf->SetXY(10, 20);
		// $pdf->writeHTML('', true, false, true, false, '');
		$pdf->SetXY(10, 5);
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
<td align="left" width="200px">Name: Testing Name</td>
<td align="left" width="100px">ATTUID: </td>
<td align="left" width="242px">Email Address: ExamapleEmail12345@gmail.com</td>
</tr>
<tr style="line-height: 20px; font-size: 9px;">
<td align="left" width="200px">Group Mailbox/Email Address &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label align="right">Region:</label></td>
<td align="left" width="100px"></td>
<td align="right" width="121px">Corporate EH&S:</td>
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
<td align="center" width="217px">WEST</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Market</td>
<td align="center" width="100px"><label style=" color: red;">RMR (CO/ID/MT/NE/UT/WY)</label></td>
<td align="center" width="217px">Desert SW Market (AZ, NM, UT, CO)</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site ID & Name</td>
<td align="center" width="100px"><label style=" color: red;">NEU3804 Alliance</label></td>
<td align="center" width="217px">NMAL00066</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >USID</td>
<td align="center" width="100px"><label style=" color: red;">150669</label></td>
<td align="center" width="217px">50431</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >FA Location Code</td>
<td align="center" width="100px"><label style=" color: red;">12854291</label></td>
<td align="center" width="217px">10093275</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Oracle Project Tracking Number</td>
<td align="center" width="100px"><label style=" color: red;">3769673998</label></td>
<td align="center" width="217px">3903A0E34Z</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >PACE Project Tracking Number</td>
<td align="center" width="100px"><label style=" color: red;">MRUTH012362</label></td>
<td align="center" width="217px">MRANM020461</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >AT&T Construction Manager</td>
<td align="center" width="100px"><label style=" color: red;">Jane Smith</label></td>
<td align="center" width="217px">Dean Lasiter</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Street Address</td>
<td align="center" width="100px"><label style=" color: red;">2503 COUNTY RD.</label></td>
<td align="center" width="217px">9700 CENTRAL SOUTHWEST</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >City</td>
<td align="center" width="100px"><label style=" color: red;">ALIANCE</label></td>
<td align="center" width="217px">ALBUQUERQUE</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >County</td>
<td align="center" width="100px"><label style=" color: red;">BOX BUTTE</label></td>
<td align="center" width="217px">BERNALILLO</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >State</td>
<td align="center" width="100px"><label style=" color: red;">NE</label></td>
<td align="center" width="217px">NM.</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Zip Code</td>
<td align="center" width="100px"><label style=" color: red;">69301</label></td>
<td align="center" width="217px">87121</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site Type</td>
<td align="center" width="100px"><label style=" color: red;">Shelter</label></td>
<td align="center" width="217px">Fenced Compound - Outdoor Cabinets</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Site Located On</td>
<td align="center" width="100px"><label style=" color: red;">Private Property - Leased</label></td>
<td align="center" width="217px">Private Property - Leased</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; border-bottom: 1px solid #fff;" >Project Type</td>
<td align="center" width="100px"><label style=" color: red;">Site Overlay.LTE NSB</label></td>
<td align="center" width="217px">DC_Plant</td>
</tr>
<tr style="line-height: 10px; font-size: 8px; ">
<td align="right" width="225px" style="border-top-color: white; " ><p>Hazardous Material Changes reported on this form:  Please Check all that apply</p></td>
<td align="center" width="100px"><p style=" color: red; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Batteries</p></td>
<td align="center" width="217px">
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Batteries &nbsp;
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Tank &nbsp;
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Generator &nbsp;
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Start Up Baterry <br>
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Cylinder &nbsp;
<input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Other-Explaine Below</td>
</tr>
<tr style="line-height: 9px; font-size: 8px; ">
<td align="right" width="225px"  ><u><i>Other Hazardous Materials</i></u> <br><br><label>Enter any other comments that might pertain to other hazardous materials changes</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="317px"></td>
</tr>
<tr style="line-height: 5px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 12px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Information</b></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="60px">GNB</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery model</td>
<td align="center" width="70px"><label style=" color: red;">Liberty 2000</label></td>
<td align="center" width="60px">M12V180FTX</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">HD-1100</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery manufacturer date</td>
<td align="center" width="70px"><label style=" color: red;">10/8/2017</label></td>
<td align="center" width="60px">9/1/2018</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Added Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">8</label></td>
<td align="center" width="60px">16</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="70px"><label style=" color: red;">Floor 4, Room 410, Northwest corner</label></td>
<td align="center" width="60px">INDOOR</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px">BATT RACK</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">2</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery <label style=" color: red;">DELIVERY</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 2px solid #fff;">Battery <label style=" color: red;">INSTALLATION</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Vendor Completing Installation</td>
<td align="center" width="70px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="60px"></td>
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
<td align="center" width="60px">GNB</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Model</td>
<td align="center" width="70px"><label style=" color: red;">NSB100FT</label></td>
<td align="center" width="60px">ABSOLYTE</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="60px">N/A</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Manufacturer Date</td>
<td align="center" width="70px"><label style=" color: red;">10/10/2010</label></td>
<td align="center" width="60px">N/A</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1.5px solid #fff;">Added Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">24</label></td>
<td align="center" width="60px">48</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style=" border-top: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Inactive - Removed</label></td>
<td align="center" width="60px">Inactive - Removed</td>
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
<td align="center" width="60px">BATTERY RACK</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px">BATT RACK</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">2</label></td>
<td align="center" width="60px">2</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery <label style=" color: red;">REMOVAL</label> Date</td>
<td align="center" width="70px"><label style=" color: red;">7/31/2018</label></td>
<td align="center" width="60px">5/29/2019</td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style=" border-top: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="70px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="60px">YOLO-TELCO</td>
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
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Model</td>
<td align="center" width="70px"><label style=" color: red;">MARATHON</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery List Number</td>
<td align="center" width="70px"><label style=" color: red;">M12V155FT</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Manufacturer Date</td>
<td align="center" width="70px"><label style=" color: red;">11/14/2013</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Existing Quantity <i style="font-size: 7px;">(Individual batteries/units)</i></td>
<td align="center" width="70px"><label style=" color: red;">12</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Battery Status</td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="70px"><label style=" color: red;">Floor 4, Room 410, Northwest corner</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cabinet Type</td>
<td align="center" width="70px"><label style=" color: red;">UMTS Cabinet</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Cabinet Number</td>
<td align="center" width="70px"><label style=" color: red;">1</label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="font-size: 7px;">Total number of battery units on-site before installation and/or removal work</td>
<td align="center" width="130px">26</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"  style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="font-size: 7px;">Total number of battery units on-site after installation and/or removal work</td>
<td align="center" width="130px">16</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"  style="border-top: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 12px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Information - California Proposition 65 Signage</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" >Is the Proposition 65 sign posted for the presence of lead-acid batteries <label style=" color: red;">(California Only)</label>?</td>
<td align="center" width="70px"><label style=" color: red;"></label></td>
<td align="center" width="60px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u><b>Battery Comments</b></u><br><label>Enter any other comments that might pertain to these batteries or the California Proposition 65 Signage</label></i></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING BATTERY PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the battery manufacturer, model, and list number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b>2</b> - Photo(s) clearly showing the total number of battery "Units"</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b>3</b>- Photo(s) clearly showing the location of the battery cabinet or rack within the equipment location as follows:</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b><u>FOR INDOOR SITES:</u></b> Photo(s) showing the location of the battery cabinet/rack for added and existing batteries in relation to the equipment room/shelter entry/doorway</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b><u>FOR OUTDOOR SITES: </u></b> Photo(s) showing the location of the battery cabinet/rack for added and existing batteries in relation to the compound entry/gateway and/or the antenna support structure/tower</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <b> 4 </b>- Photo(s) showing the location of the Proposition 65 sign (California Only)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style=" font-size: 10px;"><label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Photos - Added Batteries</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the added batteries including:  Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the total Number of batteries added</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the location of the battery cabinet or rack within the equipment/site location</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) of the removed batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"> </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Battery Photos - Existing Batteries</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of existing batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the total number of existing batteries</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the location of the battery cabinet or rack within the equipment/site location</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Proposition 65 Sign Photos <label style=" color: red;">(California Only)</label></b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) clearly showing the proposition 65 sign on the cabinet, shelter door, site access door, or compound fence</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the proposition 65 sign for the site in relation to the entrance </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 5px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
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
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">Red Top</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">SC75U</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Added Quantity (Individual batteries/units)</td>
<td align="center" width="100px"><label style=" color: red;">1</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">Active</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound in generator enclosure</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery <label style=" color: red;">DELIVERY</label> Date</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery <label style=" color: red;">INSTALLATION</label> Date</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Installation</td>
<td align="center" width="100px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="77px"></td>
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
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">Red Top</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">SC75U</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Removed Quantity <label style=" font-size: 7px;">(Individual batteries)</label></td>
<td align="center" width="100px"><label style=" color: red;">1</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">Inactive - Removed</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound in generator enclosure</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Date battery <label style=" color: red;">REMOVED</label> from site</td>
<td align="center" width="100px"><label style=" color: red;">1/1/2018</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Removal</td>
<td align="center" width="100px"><label style=" color: red;">Battery Solutions</label></td>
<td align="center" width="77px"></td>
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
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery model</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery list number</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Existing Quantity <label style=" font-size: 7px;">(Individual batteries)</label></td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Battery Status</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><b><i><u>Battery Comments</u></i></b><br><label>Enter any other comments that might pertain to these batteries</label></td>
<td align="center" width="0.1px"><label style=" color: red;"></label></td>
<td align="center" width="317px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING BATTERY PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the battery manufacturer, model, and list number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the total number of battery "Units"</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="left" width="340px">  <b>3</b> - Photo(s) showing the location of the added and existing batteries in relation to the generator/engine</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style=" font-size: 9px;"><label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Photos - Added Batteries</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the total number of  batteries added</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the location of  added batteries</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;">Photo(s) of the removed batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" style="font-size: 7px;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator Start-up Battery Photos - Existing Batteries</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) of the batteries including: Battery Manufacture - Model - List Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the total number of  existing batteries</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the location of  existing batteries</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Tank Information</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><label style=" color: red;"><i>90 DAYS PRIOR TO INSTALLATION, MODIFICATION, AND/OR REMOVAL, THE AT&T EH&S TANK TEAM MUST BE NOTIFIED OF THE PROJECT FOR APPLICABLE PERMITTING OR SPILL PREVENTION REQUIREMENTS </i></label></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Status</td>
<td align="center" width="100px"><label style=" color: red;">Active</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Type</td>
<td align="center" width="100px"><label style=" color: red;">Aboveground</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Portable or Fixed</td>
<td align="center" width="100px"><label style=" color: red;">Fixed</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Type of Fuel </td>
<td align="center" width="100px"><label style=" color: red;">Ultra Low Sulfur Diesel</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Total Storage Capacity of Tank (gallons)</td>
<td align="center" width="100px"><label style=" color: red;">210</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Description</td>
<td align="center" width="100px"><label style=" color: red;">Main Tank</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Manufacturer</td>
<td align="center" width="100px"><label style=" color: red;">American Welding & Tank</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Serial Number</td>
<td align="center" width="100px"><label style=" color: red;">55R1012A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Manufacture Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Delivery Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Install Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Installation</td>
<td align="center" width="100px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Usage</td>
<td align="center" width="100px"><label style=" color: red;">Emergency Power</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Usage Status</td>
<td align="center" width="100px"><label style=" color: red;">Currently In use</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Is the tank indoor or outdoor?</td>
<td align="center" width="100px"><label style=" color: red;">Outdoor</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="100px"><label style=" color: red;">Southwest corner of compound </label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Structure</td>
<td align="center" width="100px"><label style=" color: red;">Double Walled</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Exterior Wall Structure</td>
<td align="center" width="100px"><label style=" color: red;">Bare Steel</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Ownership</td>
<td align="center" width="100px"><label style=" color: red;">Owned</label></td>
<td align="center" width="77px"></td>
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
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">First Fuel Date</td>
<td align="center" width="100px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Removal Date MM/DD/YYYY</td>
<td align="center" width="100px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Vendor Completing Removal</td>
<td align="center" width="100px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Tank Installation Projects</u></i></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="317px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px"><label>Was approval received from EH&S through a Notice to Proceed (NTP) issued by Trinity Consultants or EBI Consulting prior to delivery/installation? </label></td>
<td align="center" width="100px"><label style=" color: red;">Yes</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Date of NTP Approval</td>
<td align="center" width="100px"><label style=" color: red;">7/31/2017</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Is the tank permitted?<br><label style=" color: red;">(NTP AND PERMITS MUST BE UPLOADED TO CCN/FILENET UNDER CONTENT/DOCUMENT ID EH309)</label></td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Type</td>
<td align="center" width="100px"><label style=" color: red;">Exempt</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permitting Agency (If Applicable)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Number (If Applicable)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Permit Expiration (Month/Year)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Does the tank have the following?</u></i></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="317px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Spill Kit (Required by local jurisdiction or for spill prevention plan)</td>
<td align="center" width="100px"><label style=" color: red;">N/A</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Spill Bucket</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Secondary Containment</td>
<td align="center" width="100px"><label style=" color: red;">None</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tank Leak Detection</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Overfill Protection</td>
<td align="center" width="100px"><label style=" color: red;">Auto Shutoff</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Cathodic Protection Present</td>
<td align="center" width="100px"><label style=" color: red;">None</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Safety Post/Traffic Bollard Protection</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Proper labeling/marking for contents (i.e. Diesel, Propane, etc.)</td>
<td align="center" width="100px"><label style=" color: red;">Yes</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px">Tertiary Containment</td>
<td align="center" width="100px"><label style=" color: red;">No</label></td>
<td align="center" width="77px"></td>
<td align="center" width="140px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="225px" ><i><u>Tank Comments</u></i><br><br><label>Enter any other comments that might pertain to these tanks</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="317px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING TANK PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Tank Photos</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Close up photo(s) of the tank information plate(s) including:  Manufacturer - Model - Serial Number - Capacity/Size - Manufacture Date - Etc.</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the total number of batteries removed</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$tbl = <<<EOD
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the location of the tank </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) of the tank piping </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) of the tank venting</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the spill kit and location</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 5px; font-size: 8px;">
<td align="center" width="332.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator and Engine Information</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><label style=" color: red;">90 DAYS PRIOR TO INSTALLATION, MODIFICATION, AND/OR REMOVAL, THE AT&T EH&S AIR TEAM MUST BE NOTIFIED OF THE PROJECT FOR PERMITTING APPLICABLE REQUIREMENTS </label></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ><b>Generator Information</b></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;"><u><i>Example</label></i></u></td>
<td align="center" width="70px"><u><i>Generator</i></u></td>
<td align="center" width="70px"><u><i>Generator (2)</i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Added, Removed, or Existing</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Added</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Status</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Active</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Type</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Internal Combustion</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Manufacturer</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Kohler</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Model</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">50REOZJE</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Serial Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">SGM32JM6S</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Output Rating Value (in kW)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Portable or Fixed</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Fixed</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Southwest corner of fenced compound </label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Delivery Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Installation Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Manufacture Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Ownership</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Owned</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Generator Removal Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31//2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-top: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">NORTHSTAR</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u><b>Cell Site Power and Generator Operation</b></u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is Generator Prime power?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is commercial power used at site?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Non-Resettable Hour Meter?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Current Hour Meter Reading?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">5</label></td>
<td align="center" width="70px"></td>
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
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff; font-size: 7px;">Date of first start-up/first fire</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="  font-size: 7px;">Is there a generator on site used for emergency power not belonging to AT&T Mobility?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u>Generator Comments</u></i><br><label  style="font-size: 7px;">Enter any other comments that might pertain to these generators</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" ><b>Engine Information</b></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;"><u><i>Example</label></i></u></td>
<td align="center" width="70px"><u><i>Engine</i></u></td>
<td align="center" width="70px"><u><i>Engine (2)</i></u></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Manufacturer</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">John Deere</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Model</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">4045TF280</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Serial Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">PE4045N003528</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Type</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Diesel Outside</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Type of Fuel</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Ultra Low Sulfur Diesel</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Manufacture Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">10/11/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Install Date (MM/DD/YYYY)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">12/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Cylinders</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">4</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Max Engine kilowatts (kW)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Rated Horsepower (HP)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">50</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Engine Max Brake Horsepower (BHP)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">85</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Displacement (Liters) or (Cubic Inches)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">4.5 Liters</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">EPA Family Number</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">HJDXL04.5141</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Diameter of stack outlet (inches)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">3</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Exhaust stack height from ground (feet)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">8</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Direction of exhaust outlet (Horizontal or Vertical)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Vertical</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">End of stack (Open or Capped)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Open</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Generator Installation Projects</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Open</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Generator Installation Projects</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Was approval received from EH&S through a Notice to Proceed (NTP) issued by Trinity Consultants or EBI Consulting prior to delivery/installation? </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date of NTP Approval</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">07/31/2017</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Is the generator/engine permitted?<br><label style="color: red;">(NTP AND PERMITS MUST BE UPLOADED TO CCN/FILENET UNDER CONTENT/DOCUMENT ID EH309)</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Type</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Exempt</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permitting Agency (If Applicable)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Number (If Applicable)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Permit Expiration (Month/Year)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">N/A</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Generator Distance (<label style=" color: red;">California Only</label>)</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest fence line (feet)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">10</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest residence (feet)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">500</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Distance from engine to nearest business (feet)</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">1000</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Is engine located within 500 feet of a school, daycare, hospital, or care facility?</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If yes, name of facility</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;"></label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If yes, distance</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;"></label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" ><i><u>Where applicable, does the engine have the following?</u></i></td>
<td align="center" width="210px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Direct Injection</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Diesel Particulate Filter</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Injection Time Retard</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Other Emission Controls</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Turbo Charger</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Diesel oxidation catalyst</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Catalytic Converter</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Intercooler</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">Yes</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="332.2px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Aftercooler</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="70px"><label style=" color: red;">No</label></td>
<td align="center" width="70px"></td>
<td align="center" width="70px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="202.2px" ><i><u>Engine Comments</u></i><br><label  style="font-size: 7px;">Enter any other comments that might pertain to these generators</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>



</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING GENERATOR AND ENGINE PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Generator and Engine Photos</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Close up photo(s) of the generator information plate(s) including:  Manufacturer - Model - Serial Number - Size - Manufacture Date - Etc.</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Close up photo(s) of the engine information plate(s) including:  Manufacturer - Model - Serial Number - Size - Manufacture Date - Etc.</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the location of the generator</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) of the left and right side of the engine compartment</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px">Generator/engine specification document to identify the engine Horsepower (HP) and Brake Horsepower (BHP)</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Information</b></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;"><b>Cylinder Information</b></td>
<td align="center" width="96px"><label style=" color: red;"><i><b><u>Example</u></b></i></label></td>
<td align="center" width="64px"><i><b><u>Cylinders Added</b></u></i></td>
<td align="center" width="78px"><i><b><u>Cylinders Added (2)</u></b></i></td>
<td align="center" width="99px"><i><b><u>Cylinders Added (3)</u></b></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>HYDROGEN</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Cylinder(s) Installed</td>
<td align="center" width="96px"><label style=" color: red;"><i>16</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>4176 ft3</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>261 ft3</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">DELIVERED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">INSTALLED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East corner of shelter</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>

</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Removed</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Cylinders Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Cylinders Removed</b></i></u></td>
<td align="center" width="78px"><u><i><b>Cylinders Removed (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Cylinders Removed (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Inactive - Removed</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>HYDROGEN</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>1566 ft3</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>261 ft3</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Removed Quantity (Individual cylinders/units)</td>
<td align="center" width="96px"><label style=" color: red;"><i>6</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Cylinder(s) <label style=" color: red;">REMOVED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East corner of shelter</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Existing (not added or removed as part of the project)</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Existing Cylinders (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Existing Cylinders</b></i></u></td>
<td align="center" width="78px"><u><i><b>Existing Cylinders (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Existing Cylinders (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Cylinder Contents</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Largest Cylinder Volume (ft3 or lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="24px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="38px"><i></i></td>
<td align="center" width="40px"><i></i></td>
<td align="center" width="70px"><i></i></td>
<td align="center" width="29px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Existing Quantity (Individual cylinders/units)</td>
<td align="center" width="96px"><label style=" color: red;"><i>0</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location of Cylinders (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Total number of Cylinder units on-site before installation and/or removal work</td>
<td align="center" width="96px" ><label style=" color: red;"><i>6</i></label></td>
<td align="center" width="64px"><i></i>0</td>
<td align="center" width="0.1px"><i></i></td>
<td align="center" width="177px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Total number of Cylinder units on-site after installation and/or removal work</td>
<td align="center" width="96px" ><label style=" color: red;"><i>16</i></label></td>
<td align="center" width="64px"><i></i>0</td>
<td align="center" width="0.1px"><i></i></td>
<td align="center" width="177px" style="border-top-color: white; border-bottom: 1px solid #010000;"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Cylinder Comments</b></u><br><label style="font-size: 7px;">Enter any other comments that might pertain to these cylinders:</label></td>
<td align="center" width="337" ><label style=" color: red;"><i></i></label></td>
</tr>

<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING CYLINDER PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Photos - Added Cylinders</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the information label on the cylinder cabinet/rack</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the number of cylinders and the cylinder location</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Photos - Removed Cylinders</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the number of cylinders removed</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>



</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Cylinder Photos - Existing Cylinders</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Close-up photo(s) clearly showing the information label on the cabinet/rack</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the label stamped on the cylinder</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the number of cylinders and the cylinder location</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Information</b></td>
<td align="center" width="0.1px"></td>
</tr>

<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="205px" ><u><i><b>Fuel Cell Information</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Example</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Fuel Cell Added</b></i></u></td>
<td align="center" width="78px"><u><i><b>Fuel Cell Added (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Fuel Cell Added (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>ReliOn-PLUG Power</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Model</td>
<td align="center" width="96px"><label style=" color: red;"><i>T-2000</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet Serial Number</td>
<td align="center" width="96px"><label style=" color: red;"><i>REL10060006</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">DELIVERED</label> to site</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">INSTALLED</label></td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>West side of compound in cabinet</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Installation</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Removed</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Fuel Cell Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Fuel Cell Removed</b></i></u></td>
<td align="center" width="78px"><u><i><b>Fuel Cell Removed (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Fuel Cell Removed (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Inactive - Removed</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>Relion</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Model</td>
<td align="center" width="96px"><label style=" color: red;"><i>T-2000</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>REL10060006</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Date Fuel Cell <label style=" color: red;">REMOVED</label> from site</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>West side of compound in cabinet</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Vendor Completing Removal</td>
<td align="center" width="96px"><label style=" color: red;"><i>NORTHSTAR</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><i><b>Existing (not added or removed as part of the project)</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Fuel Cell Removed (Example)</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>Existing Fuel Cell</b></i></u></td>
<td align="center" width="78px"><u><i><b>Existing Fuel Cell (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>Existing Fuel Cell (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Status</td>
<td align="center" width="96px"><label style=" color: red;"><i>Active</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Manufacturer</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell model</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Fuel Cell Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Gas Cylinder Cabinet serial number</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">Location Description (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Fuel Cell Comments</b></u><br><label style="font-size: 7px;">Enter any other comments that might pertain to these fuel cells</label></td>
<td align="center" width="337" ><label style=" color: red;"><i></i></label></td>
</tr>


<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING FUEL CELL PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>

</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		$pdf->AddPage();
		$tbl = <<<EOD

<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >

<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Added Fuel Cells</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the fuel cell label including manufacturer/model</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the fuel cell cabinet location</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the fuel cell cabinet and chassis serial numbers</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Removed Fuel Cells</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the removed fuel cell label including manufacturer/model</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the removed fuel cell cabinet and chassis serial numbers</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Fuel Cell Photos - Existing Fuel Cells</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="272.2px" >Photo(s) clearly showing the existing fuel cell label including manufacturer/model</td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px">Photo(s) clearly showing the existing fuel cell cabinet location</td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px">Photo(s) clearly showing the existing fuel cell cabinet and chassis serial numbers</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="272.2px" ></td>
<td align="center" width="0.1px"></td>
<td align="center" width="130px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="140px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 5px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b></b></td>
<td align="center" width="0.1px"></td>
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
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="205px" ><u><i><b>Other HazMat Information</b></i></u></td>
<td align="center" width="96px"><label style=" color: red;"><i><u><b>Example</b></u></i></label></td>
<td align="center" width="64px"><u><i><b>HazMat</b></i></u></td>
<td align="center" width="78px"><u><i><b>HazMat (2)</b></i></u></td>
<td align="center" width="99px"><u><i><b>HazMat (3)</b></i></u></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Other HazMat Added, Removed, or Existing</td>
<td align="center" width="96px"><label style=" color: red;"><i>Added</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Hazardous Material</td>
<td align="center" width="96px"><label style=" color: red;"><i>Hazardous Material</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Location  (Must include the area, floor, and room as applicable as well as the direction)</td>
<td align="center" width="96px"><label style=" color: red;"><i>East side of compound</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Type</td>
<td align="center" width="96px"><label style=" color: red;"><i>Drum</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Container Size</td>
<td align="center" width="96px"><label style=" color: red;"><i>55 gallons</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Number of Containers</td>
<td align="center" width="96px"><label style=" color: red;"><i>1</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Delivery Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Installation Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Inventory Removal Date</td>
<td align="center" width="96px"><label style=" color: red;"><i>01/01/2018</i></label></td>
<td align="center" width="64px"><i></i></td>
<td align="center" width="78px"><i></i></td>
<td align="center" width="99px"><i></i></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Refrigerants</b></u></td>
<td align="center" width="337" ><label style=" color: red;"><i></i></label></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: white; border-bottom: 1px solid #fff;">Does this site have Freon?</td>
<td align="center" width="96px"><label style=" color: red;"><i>No</i></label></td>
<td align="center" width="241px"><i></i></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" style="border-bottom-color: black; border-bottom: 1px solid #fff;">If so, how much total Freon is at the location (lbs.)</td>
<td align="center" width="96px"><label style=" color: red;"><i>N/A</i></label></td>
<td align="center" width="241px"><i></i></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><u><b>Other HazMat Comments</b></u></td>
<td align="center" width="337" ><label style=" color: red;"><i></i></label></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px" style="font-size: 12px;"><label style=" color: red;"><b><u>THE FOLLOWING FUEL CELL PHOTOS ARE REQUIRED :</u></b></label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>1</b> - Close-up photo(s) clearly showing the information label on the hazardous material container</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-bottom-color: white; border-bottom: 1px solid #fff;"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
<td align="left" width="340px">  <b>2</b> - Photo(s) clearly showing the number of hazardous materials containers and the container locations</td>
<td align="left" width="0.1px"></td>
<td align="left" width="0.1px"></td>
</tr>
<tr style="line-height: 9px; font-size: 8px;">
<td align="right" width="202.2px" style="border-top: 1px solid #fff;"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="340px">  <label style=" color: red;">ADD PHOTOS IN THE SECTION BELOW AND VERTICALLY EXPAND THE CELLS, WHERE NECESSARY</label></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; ">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b>Hazardous Materials Photos - Existing Containers</b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" >Photo(s) clearly showing the information label on the hazardous material container</td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px">Photo(s) clearly showing the number of hazardous materials containers and the container locations</td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 150px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="center" width="332.2px" > </td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="0.1px"></td>
<td align="center" width="210px"> </td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 15px; font-size: 11px; background-color: yellow;">
<td align="right" width="0.1px"  ></td>
<td align="center" width="542px"><b><label style=" color: red; ">Certification (Required for all inventory changes)</label></b></td>
<td align="center" width="0.1px"></td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Name (Please Print)</b></td>
<td align="left" width="337" >CAMERON HUDGINS</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Signature</b></td>
<td align="left" width="337" >CAMERON HUDGINS</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>AT&T Project Manager</b></td>
<td align="left" width="337" >Dean Lasiter</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Company/Vendor Responsible for Work</b></td>
<td align="left" width="337" >YOLO-TELCO</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Telephone Number and/or Email </b></td>
<td align="left" width="337" >903-426-2324</td>
</tr>
<tr style="line-height: 8px; font-size: 8px;">
<td align="right" width="205px" ><b>Date</b></td>
<td align="left" width="337" >05/30/2019</td>
</tr>


</table>
EOD;
		$pdf->writeHTML($tbl, true, false, false, false, '');
		ob_end_clean();
		$filename = "AttachmentDewormingTest.pdf";
		$path = "./uploads/";
		$pdf->Output($path . $filename, 'I');
	}


public function test_pdf2($pet_id = null)
{
	ob_start();
	$this->load->library('Pdf');
	$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
	$pdf->SetTitle('Test PDF');
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
	$pdf->Image('/uploads/sample/att_logo.jpg', 'C', 11.5, '21', '10.5', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
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
<td align="left" width="100%"><b>Today’s Date:</b></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Callers Name:</b></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Caller Phone Number(s):</b>&nbsp;&nbsp;&nbsp;&nbsp;Office:<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other:<br></td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="100%"><b>Location:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Address:<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;City:<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zip:<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;USID:<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FA Location Code:<br>
</td>
</tr>
<tr style="line-height: 10px; font-size: 9px; ">
<td align="left" width="30%" style="border-right-color: white; border-right: 1px solid #fff"><b>Longitude/Latitude (If Known):</b></td>
<td align="left" width="30%" style="border-right-color: white; border-right: 1px solid #fff">Longitude:</td>
<td align="left" width="40%">Latitude:</td>
</tr>

<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="25%" style="border-top-color: black; border-right: 1px solid #fff"><b>Requested Service Date:</b></td>
<td align="left" width="75%" style="border-top-color: black; ">Date:<br><br>Available Hours:<br></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black; "><b>Normal Working Hours:</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Site Access Requirements:</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Access Road to Facility (Dirt, Gravel, Asphalt):</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Access Requirements - Obstructions in the<br>Room for Moving the Batteries Around:</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Is the site ground level, have multiple floors? Is there<br>a freight elevator or rooftop removal needed?</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Do the batteries contain metal sleeves, modules, or<br>racking?</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Primary medium for communicating projects and<br>project scope of work details:</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Battery Handling Equipment Needed (Crane):</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Special Floor Protection Needs (Masonite Sheet):</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Any special building security issues?</b></td>
</tr>
<tr style="line-height: 17px; font-size: 9px; ">
<td align="left" width="100%" style="border-top-color: black;"><b>Any height or length restrictions for truck?</b></td>
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
	$pdf->Image('/uploads/sample/att_logo.jpg', 'C', 11.5, '21', '10.5', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
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
<td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Battery Type (Lead-acid,nickel cadmium)</td>
<td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: white; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
<td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Battery Type</td>
<td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: white; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
<td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Battery Type</td>
<td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="center" width="40%" style="border-bottom-color: black; border-right-color: white; border-left-color: black; border: 1.3px solid #fff;"></td>
<td align="left" width="25%"style="font-size: 9px; "><input type="checkbox" name="check1" id="check1" value = "checked"  readonly="true">Battery Type</td>
<td align="left" width="35%">Qty :<br><br>Weight Per Battery:<br><br>Make:<br><br>Model Numbers:</td>
</tr>
<tr style="line-height: 15px; font-size: 9px; ">
<td align="left" width="100%" ><b>Other Special Instructions:</b><br></td>
</tr>
</table>
<p><b>C&E Mobility or Turf Vendor Representative:</b></p>
<br>
<br>
<br>
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 7px; font-size: 10px; ">
<td align="left" width="15%"  style="border-color: white; border: 2px solid #fff;"><b>Name (Printed):</b><br></td>
<td align="left" width="50%" style="border-top-color: white; ">MANDON THOMPSON<br></td>
<td align="left" width="6%" style="border-color: white; border: 2px solid #fff;"><b>Date:</b><br></td>
<td align="left" width="29%" style="border-top-color: white; border-right-color: white;">06/28/2019<br></td>
</tr>
</table>
<br>
<br>
<table border="0.5" cellpadding="2" cellspacing="0" nobr="true" style="border-collapse: collapse;" >
<tr style="line-height: 7px; font-size: 10px; ">
<td align="left" width="10%"  style="border-color: white; border: 2px solid #fff;"><b>Signature:</b><br></td>
<td align="left" width="50%" style="border-top-color: white; border-right-color: white;">Signature<br></td>
</tr>
</table>
<br>
<br>
<br>
<br>
<br>
<p align="center" style="font-size: 8px"><b>AT&T PROPRIETARY (Internal Use Only)</b><br>Not for use or disclosure outside the AT&T companies, except under written agreement<br>Document is not controlled in printed or electronic copy. Obtain current version from EH&S Website at <label color="blue"><u>www.ehs.att.com</u></label></p>

EOD;


	$pdf->writeHTML($tbl, true, false, false, false, '');
	ob_end_clean();
	$filename = "AttachmentDewormingTest.pdf";
	$path = "./uploads/";
	$pdf->Output($path . $filename, 'I');
}
}