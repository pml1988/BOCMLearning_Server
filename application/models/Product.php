<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: product.php
 * Email: zhouyt.kai7@gmail.com
 */
class Product extends Eloquent{


    public function product_type()
    {
        return $this->belongs_to('ProductType');
    }

    public function product_tag()
    {
        return $this->has_many_and_belongs_to('ProductTag','product_tag_joins','product_id','product_tag_id');
    }

    public function product_attribute()
    {
        return $this->has_many_and_belongs_to('ProductAttribute','product_attribute_joins','product_id','attribute_id')
            ->with(array('value','display','sort'));
    }

    public function image()
    {
        return $this->has_many('Image');
    }

    public function product_comment()
    {
        return $this->has_many('ProductComment');
    }

    public function get_video_url()
    {
        if($this->get_attribute('video_url') != null)
            return URL::base().$this->get_attribute('video_url');
    }

    public function get_video_image_url()
    {
        if($this->get_attribute('video_url') != null)
            return URL::base().'/upload/small/'.explode('/',$this->get_attribute('video_url'))[4].'.jpg';
    }

    public function get_data_video_url()
    {
        return $this->get_attribute('video_url');
    }

    public function get_comment_count()
    {
        return ProductComment::where('product_id','=',$this->get_attribute('id'))->count();
    }


}