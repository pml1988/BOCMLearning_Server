<?php
/**
 * Author: RaymondChou
 * Date: 12-12-24
 * File: ajax.php
 * Email: zhouyt.kai7@gmail.com
 */
class Ajax_Controller extends Base_Controller {

    public $restful = true;

    public function __construct()
    {
        if(!Request::ajax()) exit;
    }

    public function get_add_attribute_html()
    {
        $sort = Input::get('sort');
        $option = '';
        foreach(ProductAttribute::get(array('id','name')) as $row)
        {
            $selected = '';
            if($row->id == \Laravel\Input::get('attribute_id'))
                $selected = 'selected="selected"';
            $option .= '<option '.$selected.' value="'.$row->id.'">'.$row->name.'</option>';
        }
        $display_select = Input::get('display') === '0' ? 'selected="selected"' : '';
        $display = '<option value="1">显示</option>'
                   .'<option '.$display_select.' value="0">折叠</option>';

        $html = '<p>字段</p>'
            .'<select id="attribute_id">'.
            $option
            .'</select>'
            .'<p>排序</p>'
            .'<input type="text" class="span1" id="attribute_sort" value="'.$sort.'"/>'
            .'<p>是否默认显示</p>'
            .'<select id="display">'.
            $display
            .'</select>'
            .'<br><p>内容</p>'
            .'<textarea id="value" class="" rows="4">'.\Laravel\Input::get('value').'</textarea>';
        return $html;
    }

    public function get_find_user()
    {
        if(is_numeric(Input::get('name')))
            $orm_obj = User::where('job_code','like',Input::get('name').'%');
        else
            $orm_obj = User::where('user_name','like','%'.Input::get('name').'%');

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $line = Auth::user()->line;
            $lines = Config::get('line');

            if($line <= 1030)
            {
                $all_lines = User::where('line','>',1030)->distinct()->get(array('line'));

                foreach ($all_lines as $all_line)
                {
                    $other_line[] = $all_line->line;
                }
            }

            if(in_array($line,$lines['self']))
            {
                if($line <= 1030)
                    $lines['self'] = array_merge($lines['self'],$other_line);
                $orm_obj = $orm_obj->where_in('line',$lines['self']);
            }
            elseif(in_array($line,$lines['company']))
            {
                if($line <= 1030)
                    $lines['company'] = array_merge($lines['company'],$other_line);
                $orm_obj = $orm_obj->where_in('line',$lines['company']);
            }
            else
            {
                if($line <= 1030)
                    $line = array_merge($line,$other_line);
                $orm_obj = $orm_obj->where('line','=',$line);
            }
        }

        $total = $orm_obj->count();

        if(Input::has('pageSize'))
            $list = $orm_obj
                ->take(Input::get('pageSize'))
                ->skip((Input::get('p')-1)*Input::get('pageSize'));
        else
            $list = $orm_obj->take(20);

        $list = $orm_obj->get(array('id','job_code','user_name as name'));

        foreach($list as &$row)
        {
            $row->name = $row->job_code.' - '.$row->name;
        }

        return \Laravel\Response::eloquent($list);
    }

    public function get_user_detail()
    {
        $user_id = Input::get('id');
        $user = User::find($user_id);

        $html = '
        <table class="table table-bordered table-hover" style="word-break:break-all;">
                <tr>
                    <td>头像</td>
                    <td><img src="'.$user->avatar_url.'" width="100px" height="100px" /></td>
                </tr>
                <tr>
                    <td>工号/EHR号</td>
                    <td>'.$user->job_code.' / '.$user->ehr_id.'</td>
                </tr>
                <tr>
                    <td>姓名</td>
                    <td>'.$user->user_name.'</td>
                </tr>
                <tr>
                    <td>性别</td>
                    <td>'.($user->sex == 0 ? '男' : '女').'</td>
                </tr>
                <tr>
                    <td>银行/部门</td>
                    <td>'.$user->bank_name.'</td>
                </tr>
                <tr>
                    <td>职位</td>
                    <td>'.$user->post.'</td>
                </tr>
                <tr>
                    <td>学历</td>
                    <td>'.$user->edu.'</td>
                </tr>
                <tr>
                    <td>薪酬等级</td>
                    <td>'.$user->wagelvl.'</td>
                </tr>
                <tr>
                    <td>入行时间</td>
                    <td>'.date('Y年m月d日',strtotime($user->enterdate)).'</td>
                </tr>
                <tr>
                    <td>手机号</td>
                    <td>'.$user->phone.'</td>
                </tr>
                <tr>
                    <td>积分/等级</td>
                    <td>'.$user->score.' / '.$user->level.'</td>
                </tr>
                <tr>
                    <td>上次登陆时间</td>
                    <td>'.$user->last_login_at.'</td>
                </tr>
        </table>
        ';
        return $html;
    }

    public function get_unzip()
    {
        $file_name    = Input::get('file_name');
        $zip_password = Input::get('zip_password');

        $path = path('storage').'hr_data/';
        $zip = $path.$file_name;

        $ex = @system('unzip -P '.$zip_password.' -d '.$path.' '.$zip,$return);

        if(substr($return, -1) == '1')
        {
            return '文件包解压成功!是否开始导入数据?';
        }
        else
        {
            @unlink($zip);
            return \Laravel\Response::error(500);
        }
    }

    public function get_do_hr()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $file_name = Input::get('file_name');
        \Laravel\Bundle::start('hr');
        HRDATA::run($file_name);
    }

    public function post_user_tree()
    {
        $partner = true;

        if(Input::has('id'))
        {
            $level = Input::get('lv');
            switch($level)
            {
                case 0 : $length = 6; break;
                case 1 : $length = 10; $partner = false; break ;
            }
            if($level == 0 AND Input::get('id') <= 1041)
            {
                $length = 10;
                $partner = false;
            }
            $orm_obj = DB::query('
                SELECT `BANKID`,`BANKNAME`
                FROM bank_infos
                WHERE length(`BANKID`) = '.$length.' AND `BANKID` LIKE "'.Input::get('id').'%"
            ');

        }
        else
        {
            if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
            {
                $line = Auth::user()->line;
                $lines = Config::get('line');
                if(in_array($line,$lines['self']))
                {
                    $where_in = implode(',',$lines['self']);
                }
                elseif(in_array($line,$lines['company']))
                {
                    $where_in = implode(',',$lines['company']);
                }
                else
                {
                    $where_in = $line;
                }

                if($line <= 1030)
                {
                    $ex = 'OR `BANKID` > 1030';
                }
                else
                {
                    $ex = '';
                }

                $orm_obj = DB::query('
                    SELECT `BANKID`,`BANKNAME`
                    FROM bank_infos
                    WHERE length(`BANKID`) = 4 AND (`BANKID` IN ('.$where_in.') '.$ex.')
                ');
            }
            else
            {
                $orm_obj = DB::query('
                    SELECT `BANKID`,`BANKNAME`
                    FROM bank_infos
                    WHERE length(`BANKID`) = 4 ');
            }
        }
        $data = array();
        foreach($orm_obj as $row)
        {
            $bank['id']       = $row->bankid;
            $bank['name']     = $row->bankname;
            $bank['isParent'] = $partner;
            $bank['halfCheck']= false;
            $bank['checked']  = false;

            $data[] = $bank;
        }
        return \Laravel\Response::json($data);

    }

    public function get_group_user()
    {
        $bank_id = explode(',',Input::get('bank_id'));
        $users = new User();

        foreach($bank_id as $bank)
        {
            if(strlen($bank) == 4)
            {
                $line_array[] = $bank;
            }
            else
            {
                $bank_id_array[] = $bank;
            }
        }

        if(isset($line_array))
        {
            $users = $users->where_in('line',$line_array);
        }

        if(isset($bank_id_array))
        {
            $users = $users->where_in('bank_id',$bank_id);
        }

        if(Input::get('time_long') == 2)
            $users = $users->where('enterdate','>',date('Ymd') - 5*10000);
        elseif(Input::get('time_long') == 3)
        {
            $users = $users->where('enterdate','>',date('Ymd') - 10*10000);
            $users = $users->where('enterdate','<=',date('Ymd') - 5*10000);
        }
        elseif(Input::get('time_long') == 4)
            $users = $users->where('enterdate','<=',date('Ymd') - 10*10000);

        $users = $users
            ->order_by('bank_id','asc')
            ->get(array('id','job_code','user_name','bank_id','enterdate'));
        $data = array();
        foreach($users as $user)
        {
            $data[] = array($user->job_code,$user->user_name,$user->bank_name,$user->work_time,'<input id="user_id" onclick="select_user(this)" value="'.$user->id.'" type="checkbox">');
        }
        $json = array('aaData' => $data);
        return \Laravel\Response::json($json);
    }


}