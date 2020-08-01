<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Texter {
    

    function sendText($body,$to) {
//         curl 'https://api.twilio.com/2010-04-01/Accounts/AC37436471bda393fe5cc202ae4a7d35fa/Messages.json' -X POST \
// --data-urlencode 'To=+16262247341' \
// --data-urlencode 'From=+16267653015' \
// --data-urlencode 'Body=Hello Happy Times' \
// -u AC37436471bda393fe5cc202ae4a7d35fa:0931382d7898f52fb6e882cd0ba1bb67
        $url = "https://api.twilio.com/2010-04-01/Accounts/AC37436471bda393fe5cc202ae4a7d35fa/Messages.json";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "To=$to&From=+16267653015&Body=" . $body);
        curl_setopt($ch, CURLOPT_USERPWD, "AC37436471bda393fe5cc202ae4a7d35fa:0931382d7898f52fb6e882cd0ba1bb67");
        $page = curl_exec($ch);

	}
	
	
}