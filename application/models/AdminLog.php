<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: AdminLog.php
 * Email: zhouyt.kai7@gmail.com
 */
class AdminLog extends Eloquent{

    public static $table = 'admin_logs';

    public function user()
    {
        return $this->belongs_to('User');
    }

}