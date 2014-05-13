<?php
/**
 * Author: RaymondChou
 * Date: 13-1-7
 * File: util.php
 * Email: zhouyt.kai7@gmail.com
 */
class Util {

    public static function get_setting($key = '')
    {
        return Setting::where('key','=',$key)->first()->value;
    }

    public static function get_role_names($roles = array())
    {
        if(!is_array($roles)) return null;
        $role_name = '';
        foreach($roles as $role)
        {
            $role_name .= Config::get('role.'.$role)['name'].' ';
        }
        return $role_name;
    }

    public static function role_describe($role)
    {
        $user_id = Auth::user()->id;
        $role_id = Config::get('role.'.$role.'.id');
        $describes = UserRole::where('user_id','=',$user_id)
            ->where('role_id','=',$role_id)
            ->first(array('describe'))->describe;
        $array = explode(',',$describes);
        return $array;
    }

    public static function save_admin_log($action = '' , $message = '')
    {
        $user_id = Auth::user()->id;
        $log = new AdminLog();

        $log->user_id = $user_id;
        $log->action  = $action;
        $log->message = $message;
        //$log->ip      = $_SERVER['HTTP_X_REAL_IP'];
        $log->save();
    }

    public static function short_number($number)
    {
        if($number/1000 < 1)
            return $number;
        else
            return number_format($number/1000,1).'k';
    }

    public static function score_plus($user_id,$rule = null)
    {
        $user = User::find($user_id);

        //加分规则
        if($rule != null)
        {
            $rule = 'score.'.$rule;
            $plus_score = (int)Setting::where('key','=',$rule)->first()->value;
        }
        else
        {
            return;
        }

        //每日最高加分
        if(Setting::where('key','=','score.daily_max')->first()->value <= $user->daily_score)
        {
            return;
        }
        else
        {
            $user->daily_score = $user->daily_score + $plus_score;
        }

        $user->score = $user->score + $plus_score;
        $user->save();
    }

    public static function user_login_score_plus($user_id)
    {
        $user = User::find($user_id);

        if(date('Y-m-d',strtotime($user->last_login_at)) != date('Y-m-d') OR $user->last_login_at == null)
        {
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->daily_score   = 0;
            $user->save();
            self::score_plus($user_id,'user_login');
        }
        else
        {
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->save();
        }
    }

    public static function comment_submit_score_plus($user_id)
    {
        self::score_plus($user_id,'comment_submit');
    }

    public static function answer_submit_score_plus($user_id)
    {
        self::score_plus($user_id,'answer_submit');
    }

    public static function question_submit_score_plus($user_id)
    {
        self::score_plus($user_id,'question_submit');
    }

    public static function best_answer_score_plus($user_id)
    {
        self::score_plus($user_id,'best_answer');
    }
}