<?php

class Role_Controller extends Base_Controller {

    public $restful = true;

	public function get_admin_role_list()
	{
        $view = View::make('role.admin_role_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = UserRole::with('user')
            ->where('role_id','=',Config::get('role.admin.id'));

        $total = $orm_obj->count();
        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page);
        $view->web_title = '系统管理员';
        return $view;
	}

    public function get_admin_role_add()
    {

        return View::make('role.admin_role_add')
            ->with('web_title','添加系统管理员');
    }

    public function post_admin_role_add()
    {

        $rules = array(
            'user_id' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $users = explode(',',Input::get('user_id'));

        foreach($users as $user)
        {
            $user_obj = User::find($user);
            $roles = unserialize($user_obj->roles);
            if($user_obj->roles == null)
                $user_obj->roles = serialize(array('admin'));
            elseif(in_array('admin',$roles))
                continue;
            else
            {
                array_push($roles,'admin');
                $user_obj->roles = serialize($roles);
            }
            $user_obj->save();

            $role = new UserRole();
            $role->user_id  = $user;
            $role->admin_id = Auth::user()->id;
            $role->role_id  = 2;
            $role->save();
        }
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改系统管理员');

        return Redirect::to('role/admin_role_list');
    }

    public function get_admin_role_del()
    {
        $role = UserRole::find(\Laravel\Input::get('id'));
        $user_id = $role->user_id;
        $role->delete();

        $user = User::find($user_id);
        $roles = unserialize($user->roles);
        if(count($roles) == 1)
            $user->roles = null;
        else
            $user->roles = serialize(array_filter($roles,function($role_name)
            {
                return ($role_name != 'admin');
            }));
        $user->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除系统管理员');

        return Redirect::back();
    }

    public function get_product_role_list()
    {
        $view = View::make('role.product_role_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = UserRole::with('user')
            ->where('role_id','=',Config::get('role.product.id'));

        $total = $orm_obj->count();
        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page);
        $view->web_title = '产品管理员';
        return $view;
    }

    public function get_product_role_add()
    {
        $types = ProductType::where('level','=',2)
            ->where('status','=',1)
            ->get(array('id','name'));

        return View::make('role.product_role_add')
            ->with('web_title','添加产品管理员')
            ->with('types',$types);
    }

    public function post_product_role_add()
    {

        $rules = array(
            'user_id'          => 'required',
            'product_type_id'  => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $users = explode(',',Input::get('user_id'));

        foreach($users as $user)
        {
            $user_obj = User::find($user);
            $roles = unserialize($user_obj->roles);
            if($user_obj->roles == null)
                $user_obj->roles = serialize(array('product'));
            elseif(in_array('product',$roles))
                continue;
            else
            {
                array_push($roles,'product');
                $user_obj->roles = serialize($roles);
            }
            $user_obj->save();

            $role = new UserRole();
            $role->user_id  = $user;
            $role->admin_id  = Auth::user()->id;
            $role->role_id  = 3;
            $role->describe = implode(',',Input::get('product_type_id'));
            $role->save();
        }
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品管理员');

        return Redirect::to('role/product_role_list');
    }

    public function get_product_role_edit()
    {
        $types = ProductType::where('level','=',2)
            ->where('status','=',1)
            ->get(array('id','name'));
        $role = UserRole::find(Input::get('id'));

        if($role == null)
            return \Laravel\Response::error(404);

        return View::make('role.product_role_edit')
            ->with('web_title','编辑产品管理员')
            ->with('role',$role)
            ->with('types',$types);
    }

    public function post_product_role_edit()
    {
        $rules = array(
            'id'               => 'required',
            'product_type_id'  => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $role = UserRole::find(Input::get('id'));
        $role->describe = implode(',',Input::get('product_type_id'));
        $role->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品管理员');

        return Redirect::to('role/product_role_list');
    }

    public function get_product_role_del()
    {
        $role = UserRole::find(\Laravel\Input::get('id'));
        $user_id = $role->user_id;
        $role->delete();

        $user = User::find($user_id);
        $roles = unserialize($user->roles);
        if(count($roles) == 1)
            $user->roles = null;
        else
            $user->roles = serialize(array_filter($roles,function($role_name)
            {
                return ($role_name != 'product');
            }));
        $user->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品管理员');

        return Redirect::back();
    }

    public function get_question_role_list()
    {
        $view = View::make('role.question_role_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = UserRole::with('user')
            ->where('role_id','=',Config::get('role.question.id'));

        $total = $orm_obj->count();
        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page);
        $view->web_title = '问答管理员';
        return $view;
    }

    public function get_question_role_add()
    {
        $types = QuestionType::where('status','=',1)
            ->get(array('id','name'));

        return View::make('role.question_role_add')
            ->with('web_title','添加问答管理员')
            ->with('types',$types);
    }

    public function post_question_role_add()
    {

        $rules = array(
            'user_id'          => 'required',
            'question_type_id'  => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $users = explode(',',Input::get('user_id'));

        foreach($users as $user)
        {
            $user_obj = User::find($user);
            $roles = unserialize($user_obj->roles);
            if($user_obj->roles == null)
                $user_obj->roles = serialize(array('question'));
            elseif(in_array('question',$roles))
                continue;
            else
            {
                array_push($roles,'question');
                $user_obj->roles = serialize($roles);
            }
            $user_obj->save();

            $role = new UserRole();
            $role->user_id  = $user;
            $role->admin_id  = Auth::user()->id;
            $role->role_id  = 4;
            $role->describe = implode(',',Input::get('question_type_id'));
            $role->save();
        }
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改问答管理员');

        return Redirect::to('role/question_role_list');
    }

    public function get_question_role_edit()
    {
        $types = QuestionType::where('status','=',1)
            ->get(array('id','name'));
        $role = UserRole::find(Input::get('id'));

        if($role == null)
            return \Laravel\Response::error(404);

        return View::make('role.question_role_edit')
            ->with('web_title','编辑问答管理员')
            ->with('role',$role)
            ->with('types',$types);
    }

    public function post_question_role_edit()
    {
        $rules = array(
            'id'                => 'required',
            'question_type_id'  => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $role = UserRole::find(Input::get('id'));
        $role->describe = implode(',',Input::get('question_type_id'));
        $role->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改问答管理员');

        return Redirect::to('role/question_role_list');
    }

    public function get_question_role_del()
    {
        $role = UserRole::find(\Laravel\Input::get('id'));
        $user_id = $role->user_id;
        $role->delete();

        $user = User::find($user_id);
        $roles = unserialize($user->roles);
        if(count($roles) == 1)
            $user->roles = null;
        else
            $user->roles = serialize(array_filter($roles,function($role_name)
            {
                return ($role_name != 'question');
            }));
        $user->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除问答管理员');

        return Redirect::back();
    }

    public function get_learn_role_list()
    {
        $view = View::make('role.learn_role_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = UserRole::with('user')
            ->where('role_id','=',Config::get('role.learn.id'));

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $orm_obj = $orm_obj->where('admin_id','=',Auth::user()->id);
        }

        $total = $orm_obj->count();
        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page);
        $view->web_title = '学习管理员';
        return $view;
    }

    public function get_learn_role_add()
    {

        return View::make('role.learn_role_add')
            ->with('web_title','添加学习管理员');
    }

    public function post_learn_role_add()
    {

        $rules = array(
            'user_id' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $users = explode(',',Input::get('user_id'));

        foreach($users as $user)
        {
            $user_obj = User::find($user);
            $roles = unserialize($user_obj->roles);
            if($user_obj->roles == null)
                $user_obj->roles = serialize(array('learn'));
            elseif(in_array('learn',$roles))
                continue;
            else
            {
                array_push($roles,'learn');
                $user_obj->roles = serialize($roles);
            }
            $user_obj->save();

            $role = new UserRole();
            $role->user_id   = $user;
            $role->admin_id  = Auth::user()->id;
            $role->role_id   = 5;
            $role->save();
        }
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改学习管理员');

        return Redirect::to('role/learn_role_list');
    }

    public function get_learn_role_del()
    {
        $role = UserRole::find(\Laravel\Input::get('id'));
        $user_id = $role->user_id;
        $role->delete();

        $user = User::find($user_id);
        $roles = unserialize($user->roles);
        if(count($roles) == 1)
            $user->roles = null;
        else
            $user->roles = serialize(array_filter($roles,function($role_name)
            {
                return ($role_name != 'learn');
            }));
        $user->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除学习管理员');

        return Redirect::back();
    }

}