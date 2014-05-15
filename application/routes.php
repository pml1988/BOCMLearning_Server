<?php

Route::get('/', function()
{
    if(!Auth::check())
    {
        return Redirect::to('login');
    }
    else
    {
        return Redirect::to('dashboard');
    }

});

Route::get('hr',function()
{
    set_time_limit(0);
    ignore_user_abort(true);

    $file_name = '20130226164610_57986.zip';
    \Laravel\Bundle::start('hr');
    HRDATA::run($file_name);
});

Route::get('test',function()
{
    Cache::forget('current_hr_status');
    Cache::forget('current_hr_time');
    Cache::forget('current_hr_file');
});

Route::get('insert',function()
{
    $file_name = path('storage').'hr_data/inserts_product.txt';
    ini_set("auto_detect_line_endings", true);
    $fp = fopen($file_name,'r');
    //逐行读取
    while(!feof($fp))
    {
        $buffer = fgets($fp);

        //整理数据
        $data_array = explode('||',$buffer);

        $product_id = $data_array[15];
        $i = 0;
        //数据库操作
        foreach($data_array as $data)
        {

            if($i<=13 && $i >= 4 && $data != null && $data != "\r")
            {
                switch($i)
                {
                    case 4: $attribute_id = 2; break;
                    case 5: $attribute_id = 31; break;
                    case 6: $attribute_id = 32; break;
                    case 7: $attribute_id = 13; break;
                    case 8: $attribute_id = 33; break;
                    case 9: $attribute_id = 34; break;
                    case 10: $attribute_id = 14; break;
                    case 11: $attribute_id = 4; break;
                    case 12: $attribute_id = 35; break;
                    case 13: $attribute_id = 36; break;
                }

                if(($i <= 5 && $i >= 4) || $i == 9 || $i == 10)
                {
                    $display = 1;
                }
                else
                {
                    $display = 0;
                }
                DB::table('product_attribute_joins')->insert(array(
                    'product_id' => $product_id,
                    'attribute_id' => $attribute_id,
                    'value' => trim(str_replace('@@',"\n",$data),'"'),
                    'sort' => $i,
                    'display' => $display
                    ));
            }

            $i++;

        }
    }
    fclose($fp);
});

Route::get('login', function()
{
    if(Auth::check())
    {
        return Redirect::to('dashboard');
    }
    $android = Version::order_by('version_code','desc')
        ->first(array('download_url'))->download_url;
    return View::make('common.login')->with('android',Helper::add_site_uri($android));
});

Route::post('login', function()
{
    $rules = array(
        'job_code'  => 'required',
        'password'  => 'required',
    );
    $validation = Validator::make(Input::get(), $rules);

    if ($validation->fails())
    {
        Messages::add('error',$validation->errors->all());
        return  Redirect::back()->with_input();
    }

    if(strlen(base64_decode(Input::get('job_code'))) < 7)
        $user = User::where_job_code(Input::get('job_code'))
            ->where_password(strtoupper(md5(Input::get('password'))))
            ->first(array('id','status','roles'));
    else
        $user = User::where_ehr_id(Input::get('job_code'))
            ->where_password(strtoupper(md5(Input::get('password'))))
            ->first(array('id','status','roles'));

    if($user != null)
    {
        if($user->status != 0 OR $user->roles == null)
        {
            Messages::add('error','您的账号不可登录后台,请联系管理员!');
            return  Redirect::back()->with_input();
        }
        else
        {
            Cache::forever('login_time_'.$user->id,Cache::get('login_now_'.$user->id,'暂无'));
            Cache::forever('login_now_'.$user->id,date('Y-m-d H:i'));
            Messages::add('success','登录成功!');
            Auth::login($user->id);
            return \Laravel\Redirect::to('dashboard');
        }
    }
    else
    {
        Messages::add('error','工号或密码错误');
        return  Redirect::back()->with_input();
    }

});

Route::get('logout', function()
{
    Auth::logout();
    Messages::add('success','您已安全退出');
    return  Redirect::to('login');
});


Route::group(array('before' => 'sign', 'after' => 'api_log'), function()
{
    //客户端接口
    Route::controller('interface');
});

Route::group(array('before' => 'role'), function()
{
    //控制面板
    Route::controller('dashboard');
    //产品管理
    Route::controller('product');
    //问答管理
    Route::controller('question');
    //用户管理
    Route::controller('user');
    //统计管理
    Route::controller('statistic');
    //系统管理
    Route::controller('system');
    //角色管理
    Route::controller('role');
    //学习管理
    Route::controller('learn');
    //上传控制器
    Route::controller('upload');
    //在线练习控制器
    Route::controller('exam');
});

//Ajax调用
Route::controller('ajax');

//事件监听

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

//路由过滤器

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

//验签过滤器
Route::filter('sign', function()
{
    $inputs = Input::get();
    if( Config::get('interface.sign_check') === true )
    {
        $sign_status = false;

        if(Input::has('sign'))
        {
            unset($inputs['sign']);
            ksort($inputs);
            foreach($inputs as $key => $value)
            {
                $input_string[] = $key.'='.$value;
            }
            if(hash_hmac('sha1', URI::current().'::'.implode('&',$input_string), Config::get('interface.key')) == Input::get('sign'))
            {
                $sign_status = true;
            }
        }

        if($sign_status === false)
        {
            if(URI::segment(1) == 'interface')
                return Response::json(array('status_code' => 403, 'error_msg' => 'sign check failed'));
            else
                return Response::error('403');
        }
    }
});

//权限过滤器
Route::filter('role', function()
{
    if($user = Auth::user())
    {
        $result = Bouncer::investigate($user)->allow_or_block_on(URI::current());
        if($result !== true) return $result;
    }
    else
    {
        return Redirect::to('login');
    }
});

//接口调用日志过滤器
Route::filter('api_log',function()
{
    $log['uri']     = URI::current();
    $log['data']    = serialize(Input::get());
    $log['method']  = Request::method();
    $log['time']    = date('Y-m-d H:i:s');
    $log['user_id'] = Input::get('user_id');
    $log['y']       = date('Y');
    $log['m']       = date('m');
    $log['d']       = date('d');

    $api_log = new ApiLog();
    $api_log->insert($log);
});