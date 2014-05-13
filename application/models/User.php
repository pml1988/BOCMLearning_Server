<?php
/**
 * Author: RaymondChou
 * Date: 12-12-26
 * File: User.php
 * Email: zhouyt.kai7@gmail.com
 */
class User extends Eloquent {

    public static $hidden = array('password');

//    public function role()
//    {
//        return $this->has_many('UserRole');
//    }

    public function get_avatar_url()
    {
        if($this->get_attribute('avatar_url') == null)
            return URL::base().'/img/noavatar.png';
        else
            return URL::base().$this->get_attribute('avatar_url');
    }

    public function get_level()
    {
        return Level::where('min_score','<=',$this->get_attribute('score'))
            ->where('max_score','>',$this->get_attribute('score'))
            ->first(array('name'))->name;
    }

    public function get_bank_name()
    {
        $bank_id = $this->get_attribute('bank_id');
        if($bank_id == null) return null;

        //缓存
        return Cache::remember('BANKID_'.$bank_id, function() use($bank_id)
        {
            return BankInfo::where('BANKID','=',$bank_id)->first()->bankname;
        }, 3600*24);
    }

    public function get_wagelvl()
    {
        $wagelvl = $this->get_attribute('wagelvl');
        if($wagelvl == null) return null;

        //缓存
        return Cache::remember('WAGELVL_'.$wagelvl, function() use($wagelvl)
        {
            return TypeInfo::where('TYPENO','=',$wagelvl)->first()->typename;
        }, 3600*24);
    }

    public function get_edu()
    {
        $edu = $this->get_attribute('edu');
        if($edu == null) return null;

        //缓存
        return Cache::remember('EDU_'.$edu, function() use($edu)
        {
            return TypeInfo::where('TYPENO','=',$edu)->first()->typename;
        }, 3600*24);
    }

    public function get_work_time()
    {
        $time = $this->get_attribute('enterdate');
        return floor((time() - strtotime($time))/(365*60*60*24)).'年';
    }

    public function get_post()
    {
        $post = $this->get_attribute('post');
        if($post == null) return null;

        //缓存
        return Cache::remember('POST_'.$post, function() use($post)
        {
            return PostInfo::where('POSTNO','=',$post)->first()->postname;
        }, 3600*24);
    }

    public function get_area()
    {
        $line = $this->get_attribute('line');
        //省行
        if($line <= 1030 && $line >= 1000)
        {
            $lines = Config::get('line');
            if(in_array($line,$lines['self']))
            {
                $str = '-个金条线-'.$line;
            }
            elseif(in_array($line,$lines['company']))
            {
                $str = '-公司条线-'.$line;
            }
            else
            {
                $str = '-未知条线-'.$line;
            }
            return '省行'.$str;
        }
        //南京地区
        elseif($line <= 1070 && $line >= 1031)
        {
            return '南京地区-'.$line;
        }
        //支行
        elseif($line <= 1099 && $line >= 1071)
        {
            return '支分行-'.$line;
        }
        else
        {
            return false;
        }
    }

//    public function set_user_name($user_name)
//    {
//        $this->set_attribute('user_name',Crypter::encrypt($user_name));
//    }
//
//    public function get_user_name()
//    {
//        return Crypter::decrypt($this->get_attribute('user_name'));
//    }


}