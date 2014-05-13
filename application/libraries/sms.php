<?php
/**
 * Author: RaymondChou
 * Date: 13-1-5
 * File: sms.php
 * Email: zhouyt.kai7@gmail.com
 */
class Sms
{

    public static function send($phone, $content)
    {
        if($phone == null) return false;
        Bundle::start('httpful');

        $content = iconv('UTF-8', 'GB2312', $content);
        $content = urlencode($content);

        $api = "http://www.sms8080.com/smssend.asp?userid=njaxy&userkey=123123&PhoneNumber={$phone}&SmsContent={$content}";

        if( Httpful::get($api)->send() == '00/1')
        {
            return true;
        }
        else
        {
            return false;
        }

    }
}