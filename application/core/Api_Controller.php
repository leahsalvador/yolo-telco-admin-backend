<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/RestController.php';
use chriskacerguis\RestServer\RestController;
class Api_controller extends CI_Controller
{
    private $menu_items;
    private $access_token;
    private $user_id;
    
    function __construct()
    {
        parent::__construct();
        $this->param = array();
        $rh = $this->input->request_headers();
        if ($this->input->server('REQUEST_METHOD') === 'GET') {
            $url = parse_url($_SERVER['REQUEST_URI']);
            parse_str($url['query'], $_GET);
            //$_GET = json_decode(@file_get_contents("php://input"), TRUE);
            $this->access_token = $this->input->get('access_token');
            $this->user_id = $this->input->get('user_id');
        }else if($this->input->server('REQUEST_METHOD') === 'POST'){
            $_POST = json_decode(@file_get_contents("php://input"), TRUE);
            $this->access_token = $rh['Authorization'];
            $this->user_id = $this->input->post('user_id');
        }
        log_message('debug', 'header ' . $this->user_id);
        log_message('debug', 'header ' . json_encode($rh));
    
        if($rh['X-Requested-With'] != 'com.cheandalison.app'){
            $response['error_title'] = 'Failed!';
            $response['error'] = "Permission Denied.";
            //$this->response( $response, 401 );
        }

        if(empty($this->access_token)){
            $response['error_title'] = 'Failed!';
            $response['error'] = "Login Session Expired. Please Login Again.";
            //$this->response( $response, 401 );
        }
        
        // Check Login session here... 
        $success = $this->check_session();
        if( ! $success){
            $response['error_title'] = 'Failed!';
            $response['error'] = "Login Session Expired. Please Login Again.";
            //$this->response( $response, 401 );//*/
        };
    }

    function check_session(){
        
        // Check if login session is not empty
        if( ! empty($this->access_token)){
            
            log_message('debug', 'Session User ID : '. $this->user_id);
            log_message('debug', 'Session Login Access Token: '. $this->access_token);

            // Load model        
            $this->load->model('account/login_session_model');
            
            // If user id is empty in session, destroy the session
            if(empty($this->user_id)){
                log_message('debug', 'Session: No User ID ');
                // Destroy the session and delete in database
                $res = $this->login_session_model->get_user_id($this->access_token)->row_array();
                if(empty($res)){
                    $this->session->sess_destroy();
                    return false;
                }
                $this->user_id = $res['user_id'];
            }

            // Check if login session exist
            $result = $this->login_session_model->get_access_token($this->user_id, $this->access_token);

            if(empty($result) || $result->num_rows() <= 0){
                log_message('debug', 'Session: Does not exists ');
                // Destroy the session and delete in database
                $this->login_session_model->delete_access_token($this->access_token);
                $this->session->sess_destroy();
                return false;
            }

            $access_token = $result->row_array();

            // Check if same OS
            if($access_token['platform'] != $this->agent->platform()){
                log_message('debug', 'Session: Not Same plat form ');
                log_message('debug', $access_token['platform']  . '!=' . $this->agent->platform());
                // Destroy the session and delete in database
                $this->login_session_model->delete_access_token($this->access_token);
                $this->session->sess_destroy();
                return false;
            }
            
            // Check if same IP
            //$httpuseragent = get_httpuseragent();
            /*if($access_token['ip_address'] != $httpuseragent['ip_address']){
                log_message('debug', 'Session: Not Same IP Addrses ');
                log_message('debug', $access_token['ip_address']  . '!=' . $httpuseragent['ip_address']);
                // Destroy the session and delete in database
                $this->login_session_model->delete_access_token($this->access_token);
                $this->session->sess_destroy();

                // Create a Flash Message Here later.
                // Your IP Address has changed. Please log in again to continue. 
                return false;
            }//*/


            // Check if login session not expired
            if($access_token['expired'] <= 0){
                log_message('debug', 'Session: expired ');
                // If expired, destroy the session and delete in database
                $this->login_session_model->delete_access_token($this->access_token);
                $this->session->sess_destroy();
                return false;
            }
            
            // If not expired, refresh then redirect to dashboard because the user is logged in.
            /*$result = $this->login_session_model->refresh_token($this->user_id, $this->access_token);
            
            // Save New Access Token
            // Check if new access token updated successfully.
            if(empty($result) || $result->num_rows() <= 0){
                log_message('debug', 'Session: Cant Update ');
                // If expired, destroy the session and delete in database
                $this->login_session_model->delete_access_token($this->access_token);
                $this->session->sess_destroy();
                return false;
            }
            
            $new_access_token = $result->row_array();

            // Update Session
            $this->session->set_userdata('access_token', $new_access_token['access_token']);

            log_message('debug', 'Session: Success ');//*/
            return true;
        }
    }

    public function get_menu_items(){
        return $this->menu_items;
    }
}