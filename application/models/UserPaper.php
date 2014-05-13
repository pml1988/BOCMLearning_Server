<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午2:52
 * File_name: UserPaper.php
 * Email: wxjajax@gmail.com
*/

class UserPaper extends Eloquent {

    public function user() {
        return $this->belongs_to('User');
    }

    public function paper() {
        return $this->belongs_to('Paper');
    }
} 