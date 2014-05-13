<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: UserTask.php
 * Email: zhouyt.kai7@gmail.com
 */
class UserRole extends Eloquent{

    public static $table = 'role_joins';

    public function user()
    {
        return $this->belongs_to('User');
    }

    public function admin()
    {
        return $this->belongs_to('User','admin_id');
    }

}