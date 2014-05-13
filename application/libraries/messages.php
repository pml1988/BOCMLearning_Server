<?php
class Messages {

     public static $msgss = array();

     /**
      * Add a message to the message array (adds to the user's session)
      * @param string  $type    You can have several types of messages, these are class names for Bootstrap's messaging classes, usually, info, error, success, warning
      * @param string $message  The message you want to add to the list
      */
     public static function add($type = 'info',$message = false)
     {
     	if(!$message) return false;
     	if(is_array($message))
         {
     		foreach($message as $msg)
             {
     			static::$msgss[$type][] = $msg;
     		 }
     	}
        else
        {
     		static::$msgss[$type][] = $message;
     	}
     	Session::flash('messages', static::$msgss);
     }

     /**
      * Pull back those messages from the session
      * @return array
      */
     public static function get()
     {
     	return Session::get('messages');
     }
    
     /**
      * Gets all the messages from the session and formats them accordingly for Twitter bootstrap.
      * @return string
      */
     public static function get_html()
     {
     	$messages = Session::get('messages');
     	$output = false;
     	if($messages)
        {
     		foreach($messages as $type=>$msgs)
            {
     			$output .= '<div class="alert alert-'.$type.' fade in"><a class="close" data-dismiss="alert">×</a>';
//                $output .= '<strong>提示</strong>';
                foreach($msgs as $msg)
                {
     				$output .= '<p>'.$msg.'</p>';
     			}
     			$output .= '</div>';
     		}
     	}
     	return $output;
     }
    
    public static function get_pines_html()
    {
        $messages = Session::get('messages');
        $output = false;
        if($messages)
        {
            foreach($messages as $type=>$msgs)
            {
                switch($type)
                {
                    case 'error' : $icon = 'cancel'; break;
                    case 'success' : $icon = 'check'; break;
                    case 'info' : $icon = 'info'; break;
                    case 'notice' : $icon = 'warning'; break;
                }
                $output .= "
                <script>
                $.pnotify({
			type: '{$type}',
		    title: '{$msgs[0]}',
		    icon: 'picon icon16 iconic-icon-{$icon}-alt white',
		    opacity: 0.95,
		    history: false,
		    sticker: false
		});
                </script>";
            }
        }
        return $output;
    }
}