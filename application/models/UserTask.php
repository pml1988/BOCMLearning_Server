<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: UserTask.php
 * Email: zhouyt.kai7@gmail.com
 */
class UserTask extends Eloquent{

    public static $table = 'task_joins';

    public function utask()
    {
        return $this->belongs_to('UTask','task_id');
    }

    public function user()
    {
        return $this->belongs_to('User');
    }

    public function group()
    {
        return $this->belongs_to('Group');
    }

}