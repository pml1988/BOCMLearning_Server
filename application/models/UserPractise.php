<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午2:41
 * File_name: UserPractise.php
 * Email: wxjajax@gmail.com
*/

class UserPractise extends Eloquent {

    public function user() {
        return $this->belongs_to('User');
    }

    public function practise() {
        return $this->belongs_to('Practise');
    }
} 