<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Answer.php
 * Email: zhouyt.kai7@gmail.com
 */
class Answer extends Eloquent{


    public function question()
    {
        return $this->belongs_to('Question');
    }

    public function user()
    {
        return $this->belongs_to('User');
    }

}