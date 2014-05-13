<?php
/**
 * Author: RaymondChou
 * Date: 13-01-24
 * File: UserGroup.php
 * Email: zhouyt.kai7@gmail.com
 */
class UserGroup extends Eloquent{

    public static $table = 'group_joins';

    public function user()
    {
        return $this->belongs_to('User');
    }

    public function group()
    {
        return $this->belongs_to('Group');
    }

}