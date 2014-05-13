<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午3:03
 * File_name: ExercisesDiscuss.php
 * Email: wxjajax@gmail.com
*/

class ExercisesDiscuss {

    public static $table = 'exercise_discuss';

    public function user() {
        return $this->belongs_to('User');
    }

    public function exercise() {
        return $this->belongs_to('Exercise');
    }

} 