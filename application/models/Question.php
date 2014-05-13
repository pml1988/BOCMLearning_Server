<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Question.php
 * Email: zhouyt.kai7@gmail.com
 */
class Question extends Eloquent{

    public function question_type()
    {
        return $this->belongs_to('QuestionType');
    }

    public function answer()
    {
        return $this->has_many('Answer');
    }

    public function user()
    {
        return $this->belongs_to('User');
    }

    public function get_had_best()
    {
        return Answer::where('question_id','=',$this->get_attribute('id'))
            ->where('is_best','=',1)
            ->count() == 1 ? true : false;
    }

    public function get_answer_count()
    {
        return Answer::where('question_id','=',$this->get_attribute('id'))
            ->count();
    }


}