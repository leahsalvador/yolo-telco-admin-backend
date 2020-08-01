<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Bangkok');

class Excel_import extends CI_Controller {

	
	public function __construct()
    {
        parent::__construct();
		$this->load->library('session');
        $this->load->model('excel_import_model');
        $this->load->library('excel');
    }

    function index(){
        
       
    }



    function isEmptyRow($row) {
        foreach($row as $cell){
            if (null !== $cell) return false;
        }
        return true;
    }


     function import(){
       
        if(isset($_FILES['file']["name"])){
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path); 
            $row = 2;
            foreach($object->getWorksheetIterator() as $worksheet){ 
              
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestRow += 1;

                for($row=2; $row<$highestRow; $row++){
                   
                    $site_name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $region = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $market_cluster = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $market = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $u_sid = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $fa_location_code = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $gsm_site_id = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $umts_site_id = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $lte_site_id = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $location_name = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $street_address = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $city = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $state = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $country = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $zip = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $latitude_dec = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $longitude_dec = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $ops_district = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $ops_zone = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $structure_type = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $site_directions = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $site_parking = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $access_detailes = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                    $monday_hours = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $tuesday_hours = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                    $wednesday_hours = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
                    $thursday_hours = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
                    $friday_hours = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
                    $saturday_hours = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
                    $sunday_hours = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
                    $access_list = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
                    $keys_combo = $worksheet->getCellByColumnAndRow(31, $row)->getValue();
                    $key_comments = $worksheet->getCellByColumnAndRow(32, $row)->getValue();
                    $notice_needed = $worksheet->getCellByColumnAndRow(33, $row)->getValue();
                    $notice_comments = $worksheet->getCellByColumnAndRow(34, $row)->getValue();
                    $ladder_lift_req = $worksheet->getCellByColumnAndRow(35, $row)->getValue();
                    $ladder_height = $worksheet->getCellByColumnAndRow(36, $row)->getValue();
                    $ladder_lift_note = $worksheet->getCellByColumnAndRow(37, $row)->getValue();
                    $site_hazard_comment = $worksheet->getCellByColumnAndRow(38, $row)->getValue();
                    $special_contract_restrictions = $worksheet->getCellByColumnAndRow(39, $row)->getValue();
                    $traccess_serial_number = $worksheet->getCellByColumnAndRow(40, $row)->getValue();
                    $traccess_location = $worksheet->getCellByColumnAndRow(41, $row)->getValue();
                    $primary_tech = $worksheet->getCellByColumnAndRow(42, $row)->getValue();
                    $cell_phone = $worksheet->getCellByColumnAndRow(43, $row)->getValue();
                    $on_call_tech = $worksheet->getCellByColumnAndRow(44, $row)->getValue();
                    $manager = $worksheet->getCellByColumnAndRow(45, $row)->getValue();
                
                    $data[] = array(
                        "site_name" => $site_name,
                        "region" => $region,
                        "market_cluster" => $market_cluster,
                        "market" => $market,
                        "u_sid" => $u_sid,
                        "fa_location_code" => $fa_location_code,
                        "gsm_site_id" => $gsm_site_id,
                        "umts_site_id" => $umts_site_id,
                        "lte_site_id" => $lte_site_id,
                        "location_name" => $location_name,
                        "street_address" => $street_address,
                        "city" => $city,
                        "state" => $state,
                        "country" => $country,
                        "zip" => $zip,
                        "latitude_dec" => $latitude_dec,
                        "longitude_dec" => $longitude_dec,
                        "ops_district" => $ops_district,
                        "ops_zone" => $ops_zone,
                        "structure_type" => $structure_type,
                        "site_directions" => $site_directions,
                        "site_parking" => $site_parking,
                        "access_detailes" => $access_detailes,
                        "monday_hours" => $monday_hours,
                        "tuesday_hours" => $tuesday_hours,
                        "wednesday_hours" => $wednesday_hours,
                        "thursday_hours" => $thursday_hours,
                        "friday_hours" => $friday_hours,
                        "saturday_hours" => $saturday_hours,
                        "sunday_hours" => $sunday_hours,
                        "access_list" => $access_list,
                        "keys_combo" => $keys_combo,
                        "key_comments" => $key_comments,
                        "notice_needed" => $notice_needed,
                        "notice_comments" => $notice_comments,
                        "ladder_lift_req" => $ladder_lift_req,
                        "ladder_height" => $ladder_height,
                        "ladder_lift_note" => $ladder_lift_note,
                        "site_hazard_comment" => $site_hazard_comment,
                        "special_contract_restrictions" => $special_contract_restrictions,
                        "traccess_serial_number" => $traccess_serial_number,
                        "traccess_location" => $traccess_location,
                        "primary_tech" => $primary_tech,
                        "cell_phone" => $cell_phone,
                        "on_call_tech" => $on_call_tech,
                        "manager" => $manager
                    );
                }
               
            }
            $this->excel_import_model->insert($data);
            $this->excel_import_model->delete_null_site_rows();
        }else{
            echo 'form file didnt recieved';
        }
    }




    
}//====================controller closing