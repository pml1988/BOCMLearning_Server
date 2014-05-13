<?php
/**
 * Author: RaymondChou
 * Date: 13-1-11
 * File: notify.php
 * Email: zhouyt.kai7@gmail.com
 */

class Hr_Task {

    public function run()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $file_name = '20130123164401_42369.zip';
        \Laravel\Bundle::start('hr');
        HRDATA::run($file_name);
    }

}