<?php

class User_Controller extends Base_Controller {

    public $restful = true;

	public function get_user_list()
	{
        $view = View::make('user.user_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 30;

        $orm_obj = User::order_by('id','asc');

        if(Input::has('search_name'))
        {
            $orm_obj = $orm_obj->where('user_name','like','%'.Input::get('search_name').'%');
        }
        if(Input::has('search_job_code'))
        {
            $orm_obj = $orm_obj->where('job_code','=',Input::get('search_job_code'))
                ->or_where('ehr_id','=',Input::get('search_job_code'));
        }

        $total = $orm_obj->count();
        $view->list = $orm_obj
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'search_name'     => Input::get('search_name'),
                'search_job_code' => Input::get('search_job_code')
            ));
        $view->web_title = '用户管理';
		return $view;
	}

    public function get_level()
    {
        $level = Level::all();

        return View::make('user.level')
            ->with(array(
                'web_title' => '积分管理',
                'level'     => $level
            ));
    }

    public function post_level_edit()
    {
        for($i=1;$i<=9;$i++)
        {
            $rules = array(
                'name_'.$i      => 'required|max:6|min:1',
                'min_score_'.$i => 'required|integer',
                'max_score_'.$i => 'required|integer'
            );

            $validation = Validator::make(Input::get(), $rules);
            if ($validation->fails())
            {
                Messages::add('error',$validation->errors->all());
                return  Redirect::back()
                    ->with_input();
            }

            $orm_obj = Level::find($i);
            $orm_obj->name = Input::get('name_'.$i);
            $orm_obj->min_score = Input::get('min_score_'.$i);
            $orm_obj->max_score = Input::get('max_score_'.$i);
            $orm_obj->save();
        }
        Messages::add('success','操作成功!');
        return Redirect::to('user/level');
    }

    public function post_score_edit()
    {
        foreach(Input::get() as $key => $value)
        {
            $key = 'score.'.$key;
            $setting = Setting::where('key','=',$key)->first();
            $setting->value = $value;
            $setting->save();
        }
        Messages::add('success','操作成功!');
        return Redirect::to('user/level');
    }

    public function get_tree()
    {
        return View::make('user.tree')
            ->with(array('web_title' => '组织结构'));
    }

    public function post_tree_list()
    {
        $partner = true;

        if(Input::has('id'))
        {
            $level = Input::get('lv');
            switch($level)
            {
                case 0 : $length = 4; break;
                case 1 : $length = 6; break;
                case 2 : $length = 10; $partner = false; break;
            }
            if($level == 1 AND Input::get('id') <= 1041)
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
            $orm_obj = BankInfo::where('BANKID','=',10)
                    ->get(array('BANKID','BANKNAME'));
        }
        $data = array();
        foreach($orm_obj as $row)
        {
            $bank['id']       = $row->bankid;
            $bank['name']     = $row->bankname;
            $bank['isParent'] = $partner;

            $data[] = $bank;
        }
        return \Laravel\Response::json($data);

    }



}