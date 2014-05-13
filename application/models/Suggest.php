<?php
/**
 * Author: RaymondChou
 * Date: 12-12-21
 * File: Suggest.php
 * Email: zhouyt.kai7@gmail.com
 */
class Suggest extends Eloquent{

    public function user()
    {
        return $this->belongs_to('User');
    }

}