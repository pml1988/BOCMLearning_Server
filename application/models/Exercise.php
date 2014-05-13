<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午2:20
 * File_name: Exercise.php
 * Email: wxjajax@gmail.com
 */

class Exercise extends Eloquent {

    public function pool() {
        return $this->belongs_to('Pool');
    }

} 