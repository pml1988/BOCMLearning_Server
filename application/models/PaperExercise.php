<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午2:55
 * File_name: PaperExercise.php
 * Email: wxjajax@gmail.com
*/

class PaperExercise extends Eloquent {

    public function paper() {
        return $this->belongs_to('Paper');
    }

    public function exercise() {
        return $this->belongs_to('Exercise');
    }
} 