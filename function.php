	function send_email($to,$subject,$message,$substitutions=null,$template_id="default") //send email
	{
				
		//Include SendGrid API Library
		require_once(APPPATH.'third_party/sendgrid-php/sendgrid-php.php');

		
		//API Key for authentication
		$apiKey = $this->getconfig("email_sendgrid_api_key");
		$sg = new \SendGrid($apiKey);
		
		//Set template
		$template = $this->getconfig("email_sendgrid_template_".$template_id);

		
		//Mail Footer (Only in effect if used in template.)
		$emailfooter = $this->getconfig("email_footer");
				
		//Substitutions append message to other substitutions for easier template development.
		$substitutions .= '"-message-": "' . $message . '"';
		
		
		$request = '{
		  "content": [
		    {
		      "type": "text/html", 
		      "value": "<html>'.$message.'</html>"
		    }
		  ], 
		  "from": {
		    "email": "'. $this->getconfig("email_from_address") .'", 
			"name": "'. $this->getconfig("email_from_name") .'"
		  }, 
		  "headers": {}, 
		  "mail_settings": {
		    "bypass_list_management": {
		      "enable": true
		    }, 
		    "footer": {
		      "enable": true, 
		      "html": "<p></p>", 
		      "text": "' . $emailfooter . '"
		    }, 
		    "sandbox_mode": {
		      "enable": false
		    }, 
		    "spam_check": {
		      "enable": true, 
		      "post_to_url": "http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] .'", 
		      "threshold": 3
		    }
		  }, 
		  "personalizations": [
		    {
		      "subject": "'.$subject.'",
		      "headers": {
		        "X-Accept-Language": "en", 
		        "X-Mailer": "SendGrid"
		      }, 
		    "substitutions": {
				'. $substitutions .'
	      		}, 
		      "to": [
		        {
		          "email": "' . $to .'" //Allowing use of substitutions within template development.
		        }
		      ]
		    }
		  ], 
		  "template_id": "'.$template.'", 
		  "tracking_settings": {
		    "click_tracking": {
		      "enable": true, 
		      "enable_text": true
		    }
		  }
		}';
	
		$request_body = json_decode($request);
		$response = $sg->client->mail()->send()->post($request_body);

      //Add log functions or response checking here.
      //Otherwise just return TRUE for quick testing.
		return TRUE;

	}
