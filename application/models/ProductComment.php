<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: ProductComment.php
 * Email: zhouyt.kai7@gmail.com
 */
class ProductComment extends Eloquent{

    public static $table = 'product_comments';

    public function product()
    {
        return $this->belongs_to('Product');
    }

    public function user()
    {
        return $this->belongs_to('User');
    }

}