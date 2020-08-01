<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Bangkok');

class Site extends CI_Controller
{
        public function index()
        { 
                
        }

        public function manage_site()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $sites = $this->site_model->all_sites();
                echo json_encode($sites);
        }

        public function get_site_task_manage()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $site_task = $this->site_model->get_site_task_manage($_POST['site_id']); //
                echo json_encode($site_task);
        }

        public function get_site_task()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $site_task = $this->site_model->get_site_task($_POST['site_id']); //
                echo json_encode($site_task);
        }

        public function get_the_name_of_user_the_site_was_assigned()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $name_the_task_was_assigned = $this->site_model->get_the_name_of_user_the_site_was_assigned($_POST['site_id']); //
                echo json_encode($name_the_task_was_assigned);
        }
        //==========================================================================================
        public function get_search_result()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $result = $this->site_model->get_search_result($_POST['search']);
                echo json_encode($result);
        }

        public function show_search_result()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $sites = $this->site_model->get_site_search_result($_POST['site_id']);
                echo json_encode($sites);
        }

        public function get_all_site_that_was_already_assigned()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $assigned_task = $this->site_model->get_all_task_that_was_assigned();
                echo json_encode($assigned_task);
        }
        //============================================================================================

        public function get_search_site_result()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $sites_result = $this->site_model->search_site_for_adding_video_instruction($_POST['search_name']);
                echo json_encode($sites_result);
        }

        public function show_search_site_result_for_sending_vid()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $sites = $this->site_model->get_site_search_result($_POST['site_id']);
                echo json_encode($sites);
        }

        public function reschedule_task()
        {
                $this->load->model('site_model');
                $this->load->library('session');
                $task_date = array("task_date" => $_POST['task_date'],
                );
                $this->site_model->reschedule_task($_POST['task_id'], $task_date);
                echo "success";
        }
        //==========================================sites PDF=========================
        public function site_pdf($site_id = null)
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
<td align="right" width="225px"  ><u><i>Other Hazardous Materials</i></u> <br><br><label>Enter any other comments 
that might pertain to other hazardous materials changes</label></td>
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
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_1" height= "130" width="272.2">
EOD;
                }
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
if ($site->battery_photo_2 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_2" height= "130" width="130px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
if ($site->battery_photo_3 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_3" height= "130" width="140px">
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
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_4" height= "130" width="332px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->battery_photo_5 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_5" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_6" height= "130" width="272px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
if ($site->battery_photo_7 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_7" height= "130" width="130px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
if ($site->battery_photo_8 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_8" height= "130" width="140px">
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
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_9" height= "130" width="332px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->battery_photo_10 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->battery_photo_10" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_1" height= "130" width="272.2px">
EOD;
                }
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
if ($site->generator_battery_photo_2 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_2" height= "130" width="130px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
if ($site->generator_battery_photo_3 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_3" height= "130" width="140px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_4" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->generator_battery_photo_5 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_5" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_6" height= "130" width="272.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="130px">
EOD;
if ($site->generator_battery_photo_7 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_7" height= "130" width="130px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="140px">
EOD;
if ($site->generator_battery_photo_8 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_battery_photo_8" height= "130" width="140px">
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
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_1" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->fuel_tank_photo_2 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_2" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_3" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->fuel_tank_photo_4 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_4" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_5" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->fuel_tank_photo_6 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->fuel_tank_photo_6" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_1" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->generator_and_engine_photo_2 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_2" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_3" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->generator_and_engine_photo_4 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_4" height= "130" width="210px">
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
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_5" height= "130" width="332.2px">
EOD;
}
$tbl .= <<<EOD
</td>
<td align="center" width="210px">
EOD;
if ($site->generator_and_engine_photo_6 !== "") {
$tbl .= <<<EOD
<img src="https://192.168.1.7/telco/uploads/$site->generator_and_engine_photo_6" height= "130" width="210px">
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
$filename = "AttachmentDewormingTest.pdf";
$path = "./uploads/";
$pdf->Output($path . $filename, 'I');
        }

        public function testing()
        {
                $hazardous_material = "Testing Battery";
                $hazardous_material_data = '';
                if (strpos($hazardous_material, 'Battery') !==  false) {
echo '<input type="checkbox" name="check1" id="check1" value = "checked"  checked="checked" readonly="true">Batteries &nbsp;';
                }
                echo $hazardous_material_data;
                echo "//////////";
        }

        public function disposal()
        {
                echo 'test';
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
                $filename = "AttachmentDewormingTest.pdf";
                $path = "./uploads/";
                $pdf->Output($path . $filename, 'I');
        }
}