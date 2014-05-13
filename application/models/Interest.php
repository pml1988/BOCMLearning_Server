<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Interest.php
 * Email: zhouyt.kai7@gmail.com
 */
class Interest extends Eloquent{


    public function user()
    {
        return $this->belongs_to('User');
    }

    public function product_type()
    {
        return $this->belongs_to('ProductType');
    }
}