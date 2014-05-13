<?php
/**
 * Author: RaymondChou
 * Date: 12-12-20
 * File: product_type.php
 * Email: zhouyt.kai7@gmail.com
 */
class ProductType extends Eloquent{

    public static $table = 'product_types';

    public function product()
    {
        return $this->has_many('Product');
    }

    public function get_level_count()
    {
        return $this
            ->where('top_id','=',$this->get_attribute('id'))
            ->count();
    }

    public function get_top_name()
    {
        return $this
            ->where('id','=',$this->get_attribute('top_id'))
            ->first(array('name'));
    }
}