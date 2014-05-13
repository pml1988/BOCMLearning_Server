<?php
/**
 * Author: RaymondChou
 * Date: 12-12-20
 * File: QuestionType.php
 * Email: zhouyt.kai7@gmail.com
 */
class QuestionType extends Eloquent{

    public static $table = 'question_types';

    public function question()
    {
        return $this->has_many('Question');
    }
}