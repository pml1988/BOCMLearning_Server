<?php

class System_Controller extends Base_Controller {

    public $restful = true;

	public function get_version_list()
	{
        $view = View::make('system.version_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $total = Version::count();

        $view->list = Version::
            order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '手机客户端版本管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page);

        return $view;
	}

    public function get_version_add()
    {
        return View::make('system.version_add')
            ->with(array('web_title' => '添加客户端版本'));
    }

    public function post_version_add()
    {
        $rules = array(
            'content'      => 'required|max:256|min:2',
            'version'      => 'required',
            'version_code' => 'required',
            'download_url' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $version = new Version();
        $version->content = Input::get('content');
        $version->version = Input::get('version');
        $version->version_code = Input::get('version_code');
        $version->download_url = Input::get('download_url');
        $version->is_force = Input::has('is_force') ? 1 : 0;
        $version->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加客户端版本');

        //推送给安卓客户端
        $msg_content = array(
            'n_title'   => 'M-Learning 已有新版本可升级',
            'n_content' => '江苏中行M-Learning有一个新的版本('.Input::get('version').'),点击升级',
            'n_extras'  => array(
                'action'       => 'new_version'
            )
        );
        Bundle::start('jpush');
        $jpush = new Jpush();
        $jpush->send(10001, 4, '', 1, json_encode($msg_content), 'android');

        return Redirect::to('system/version_list');
    }

    public function get_version_edit()
    {
        $version = Version::find(Input::get('id'));

        if($version == null)
            return \Laravel\Response::error(404);

        return View::make('system.version_edit')
            ->with('web_title','编辑客户端版本')
            ->with('version',$version);
    }

    public function post_version_edit()
    {
        $rules = array(
            'content'      => 'required|max:256|min:2',
            'version'      => 'required',
            'version_code' => 'required',
            'download_url' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $version = Version::find(Input::get('id'));
        $version->content = Input::get('content');
        $version->version = Input::get('version');
        $version->version_code = Input::get('version_code');
        $version->download_url = Input::get('download_url');
        $version->is_force = Input::has('is_force') ? 1 : 0;
        $version->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改客户端版本');

        return Redirect::to('system/version_list');
    }

    public function get_version_del()
    {
        $version = Version::find(\Laravel\Input::get('id'));
        $version->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除客户端版本');

        return Redirect::back();
    }

    public function get_setting()
    {
        return View::make('system.setting')
            ->with(array(
                'web_title' => '系统设置',
            ));
    }

    public function post_setting()
    {
        foreach(Input::get() as $key => $value)
        {
            $setting = Setting::where('key','=',$key)->first();
            $setting->value = $value;
            $setting->save();
        }
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改系统设置');

        return Redirect::to('system/setting');
    }

    public function get_suggest_list()
    {
        $view = View::make('system/suggest_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $total = Suggest::count();

        $view->list = Suggest::with('user')
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '意见反馈';
        $view->page_link = Paginator::make($view->list, $total, $per_page);

        return $view;
    }

    public function get_hr_data()
    {
        return View::make('system.hr_data')
            ->with(array(
                'web_title' => 'HR数据导入',
            ));
    }

}