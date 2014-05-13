<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Group.php
 * Email: zhouyt.kai7@gmail.com
 */
class Group extends Eloquent{

    public function admin()
    {
        return $this->belongs_to('User','admin_id');
    }

    public function get_icon_url()
    {
        if($this->get_attribute('icon_url') == null)
            return URL::base().'/img/teambig.png';
        else
            return URL::base().$this->get_attribute('icon_url');
    }

}