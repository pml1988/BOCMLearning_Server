<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Task.php
 * Email: zhouyt.kai7@gmail.com
 */
class UTask extends Eloquent{

    public static $table = 'tasks';

    public function admin()
    {
        return $this->belongs_to('User','admin_id');
    }

    public function group()
    {
        return $this->belongs_to('Group');
    }

}