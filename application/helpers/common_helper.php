<?php
require_once (BASEPATH."../application/config/access.php");

	function pr($arr, $option="")
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		if ($option != "") {
			exit();
		}
	}

    function format_date($date,$format='d/M/Y')
    {
        return date($format, strtotime($date));
    }

	function public_path($type="www")
	{
		return base_url()."public/";
	}

    function profile_img_path($type="www")
    {
        return base_url()."uploads/profile_images/";
    }

    function category_img_path($type="www")
    {
        return base_url()."uploads/category_images/";
    }

	function getSetting($var)
	{

		$CI =& get_instance();
		$setting = $CI->session->userdata('setting');
		if ($setting)
		{
			return $setting[$var];
		}
		else
		{
			$settings = $CI->common_model->selectData(SETTING, '*');
			$csettings = array();
			foreach($settings as $setting)
			{	
				$csettings[$setting->option_name] = $setting->option_field;
			}
			$CI->session->set_userdata('setting',$csettings);
			return $csettings[$var];
		}
	}

    function is_front_login()
    {

        $CI =& get_instance();
        $session = $CI->session->userdata('front_session');

        if (!isset($session['id'])) {
            redirect(base_url());
        }
    }

	function success_msg_box($msg)
	{
		$html = '<div class="alert alert-success alert-dismissable">
                    <i class="fa fa-check"></i>
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    '.$msg.'
                </div>';
        return $html;
	}

	function error_msg_box($msg)
	{
		$html = '<div class="alert alert-danger alert-dismissable">
                    <i class="fa fa-ban"></i>
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    '.$msg.'
                </div>';
        return $html;
	}

	function get_active_tab($tab)
    {
    	$CI =& get_instance();
        if ($CI->router->fetch_class() == $tab) {
            return 'active';
        }
    }


    function sendEmail($to, $subject, $emailTpl, $from, $from_name, $cc='', $bcc=''){
        $CI =& get_instance();

        $CI->load->library('email');

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $CI->email->initialize($config);

        $CI->email->from($from, $from_name);
        $CI->email->to($to);

        if($cc != ''){
            $CI->email->cc($cc);
        }

        if($bcc != ''){
            $CI->email->bcc($bcc);
        }

        $CI->email->subject($subject);
        $CI->email->message($emailTpl);

        $email_Sent = $CI->email->send();
        return $email_Sent;
    }

	function replace_char($str)
	{
		return str_replace(array(",","/","(",")","&","%"," ","@"),"-",trim($str));
	}


    function curl_request($url,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

	function hasAccess($class,$method = "index")
	{
		global $access;
		$CI =& get_instance();
		$user = $CI->session->userdata('user_session');
		$role = $user['role'];
		$page= $access[$class][$method]; 


		if (!isset($user['role'])) 
			return false;

		if (!in_array($role,$page))
			return false;

		return true;
	}
?>
