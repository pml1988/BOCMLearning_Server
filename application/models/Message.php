<?php
/**
 * Author: RaymondChou
 * Date: 12-12-26
 * File: Message.php
 * Email: zhouyt.kai7@gmail.com
 */
class Message extends Eloquent {

    public function user()
    {
        return $this->belongs_to('User');
    }
}