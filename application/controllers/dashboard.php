<?php

class Dashboard_Controller extends Base_Controller {

    public $restful = true;

	public function get_index()
	{

        $logs = AdminLog::where('user_id','=',Auth::user()->id)
            ->order_by('created_at','desc')
            ->take(10)
            ->get();
		return View::make('dashboard.index')->with(array(
            'web_title' => '控制台',
            'logs' => $logs,
            'login_time' => Cache::get('login_time_'.Auth::user()->id,'暂无')
        ));
	}

}