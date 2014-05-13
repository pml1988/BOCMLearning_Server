<?php
/**
 * 极光推送JPUSH
 */

class Jpush {

	private $_master_secret = 'f37aac6fbf658aeaea1a3ffb';
	private $_appkeys = '795c068d73c0cb1431c1c6bf';

    function request_post($url = '', $param = '')
    {
		if (empty($url) || empty($param))
        {
			return false;
		}

		$postUrl = $url;
		$curlPost = $param;

		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL,$postUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = curl_exec($ch);//运行curl
		curl_close($ch);
		
		return $data;
	}

	function send($sendno = 0, $receiver_type = 4, $receiver_value = '', $msg_type = 1, $msg_content = '', $platform = 'android,ios')
    {
		$url = 'http://api.jpush.cn:8800/sendmsg/v2/sendmsg';
		
		$param = 'sendno='.$sendno;

		$appkey = $this->_appkeys;

		$param .= '&app_key='.$appkey;

		$param .= '&receiver_type='.$receiver_type;

        $param .= '&receiver_value='.$receiver_value;

		$verification_code = strtoupper(md5($sendno.$receiver_type.$receiver_value.$this->_master_secret));

		$param .= '&verification_code='.$verification_code;

		$param .= '&msg_type='.$msg_type;

		$param .= '&msg_content='.$msg_content;

		$param .= '&platform='.$platform;

        $param .= '&time_to_live=864000';
				
		$res = $this->request_post($url, $param);

		if ($res === false)
        {
			return false;
		}	
		$res_arr = json_decode($res, true);

        if($res_arr['errcode'] != '0')
        {
            return false;
        }
        else
        {
            return true;
        }
	}
	
}