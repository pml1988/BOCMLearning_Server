<?php
/**
 * Author: RaymondChou
 * Date: 13-03-19
 * File: Chat.php
 * Email: zhouyt.kai7@gmail.com
 */
class Chat extends Eloquent{

    public static $timestamps = false;

    function user()
    {
        return $this->belongs_to('User','uid');
    }

}