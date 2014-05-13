<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Image.php
 * Email: zhouyt.kai7@gmail.com
 */
class Image extends Eloquent{


    public function product()
    {
        return $this->belongs_to('Product');
    }

    public function get_url()
    {
        return URL::base().$this->get_attribute('url');
    }

    public function get_data_url()
    {
        return $this->get_attribute('url');
    }

    public function get_small_image_url()
    {
        return URL::base().'/upload/small/s_'.explode('/',$this->get_attribute('url'))[4];
    }

}