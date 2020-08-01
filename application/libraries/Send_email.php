<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Send_email {
    
    function sending_mail($body,$subject,$to) {
    	 $CI =& get_instance();
    	 
    	 $extention = explode("@",$to);
    	 if($extention[1] == 'yahoo.com') {
    	 	$CI->load->library('email'); 
	        $result = $CI->email
					        ->from('contact@balancedchef.com')
					        //->reply_to('vincentarmedilla@yahoo.com')    // Optional, an account where a human being reads.
					        ->to($to)
					        ->subject($subject)
					        ->message($body)
					        ->send();

			if($result) {
				echo '1';
			} else {
				echo '0';
			}
    	 	
    	 } else {
			$CI->load->model("mailer");
    	 	$CI->mailer->send($body,"Your Order has been processed.",$to);
		 }
	}
	
	function send_mail_forgot($body,$subject,$to) {
		$CI =& get_instance();
    	 
    	 $extention = explode("@",$to);
    	 if($extention[1] == 'yahoo.com') {
    	 	$CI->load->library('email'); 
	        $result = $CI->email
					        ->from('contact@balancedchef.com')
					        //->reply_to('vincentarmedilla@yahoo.com')    // Optional, an account where a human being reads.
					        ->to($to)
					        ->subject($subject)
					        ->message($body)
					        ->send();

			
    	 } else {
			$CI->load->model("mailer");
    	 	$CI->mailer->send($body,"Your Order has been processed.",$to);
		 }
	}
}