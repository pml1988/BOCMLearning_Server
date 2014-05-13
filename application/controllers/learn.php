<?php

class Learn_Controller extends Base_Controller {

    public $restful = true;

	public function get_group_list()
    {
        $view = View::make('learn.group_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = new Group();

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
        $view->web_title = '学习小组管理';
        return $view;
    }

    public function get_group_add()
    {
        return View::make('learn.group_add')
            ->with('web_title','添加学习小组');
    }

    public function post_group_add()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
            'num'   => 'required|max:8|unique:groups,num'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $data['name']       = Input::get('name');
        $data['admin_id']   = Auth::user()->id;
        $data['admin_name'] = Auth::user()->user_name;
        $data['num']        = Input::get('num');
        $data['detail']     = Input::get('detail');
        $data['icon_url']   = Input::get('icon_url');
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = DB::table('groups')->insert_get_id($data);

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加学习小组');

        return Redirect::to('learn/group_user_list?id='.$id);
    }

    public function get_group_edit()
    {
        $view = View::make('learn.group_edit');

        $view->group = Group::find(Input::get('id'));
        $view->web_title = '编辑学习小组';
        return $view;
    }

    public function post_group_edit()
    {
        $rules = array(
            'id'    => 'required',
            'name'  => 'required|max:16|min:2'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $group = Group::find(Input::get('id'));

        $group->name       = Input::get('name');
        $group->detail     = Input::get('detail');
        $group->icon_url   = Input::has('icon_url') ? Input::get('icon_url') : null;
        $group->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改学习小组');

        return Redirect::to('learn/group_user_list?id='.Input::get('id'));
    }

    public function get_group_del()
    {
        $group = Group::find(\Laravel\Input::get('id'));
        $group->delete();

        //连带删除
        UserGroup::where('group_id','=',Input::get('id'))
            ->delete();

        UserTask::where('group_id','=',Input::get('id'))
            ->delete();

        UTask::where('group_id','=',Input::get('id'))
            ->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除学习小组');

        return Redirect::back();
    }

    public function get_group_user_list()
    {
        $view = View::make('learn.group_user_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $group = Group::find(Input::get('id'));
        if($group == null) return \Laravel\Response::error(404);

        $orm_obj = UserGroup::where('group_id','=',Input::get('id'));

        if(Input::get('s') == 'free')
        {
            $orm_obj = $orm_obj->where('is_freedom','=',1);
        }
        elseif(Input::get('s') == 'origin')
        {
            $orm_obj = $orm_obj->where('is_freedom','=',0);
        }

        $total = $orm_obj->count();

        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'id' => Input::get('id'),
                's'  => Input::get('s')
            ));
        $view->web_title = '学习小组 - '.$group->name;
        return $view;
    }

    public function get_group_user_add()
    {
        return View::make('learn.group_user_add')
            ->with('web_title','添加学习小组成员');
    }

    public function post_group_user_add()
    {
        $rules = array(
            'user_id'  => 'required',
            'group_id' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $tasks = UTask::where('group_id','=',Input::get('group_id'))
            ->where('status','=',1);

        foreach(Input::get('user_id') as $user_id)
        {
            $has_add = UserGroup::where('group_id','=',Input::get('group_id'))
                ->where('user_id','=',$user_id)
                ->count();
            if($has_add > 0) continue;

            $group_user = new UserGroup();
            $group_user->user_id    = $user_id;
            $group_user->group_id   = Input::get('group_id');
            $group_user->is_freedom = 0;
            $group_user->save();

            if($tasks->count() > 0)
            {
                //有已发布的任务,则继承任务
                $u_tasks = $tasks->get();

                foreach($u_tasks as $task)
                {
                    $user_task = new UserTask();
                    $user_task->task_id   = $task->id;
                    $user_task->group_id  = $task->group_id;
                    $user_task->user_id   = $user_id;
                    $user_task->completed = serialize(array());
                    $user_task->save();
                }
            }
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加学习小组成员');

        return Redirect::to('learn/group_user_list?id='.Input::get('group_id'));
    }

    public function get_group_user_del()
    {
        $group = UserGroup::find(\Laravel\Input::get('id'));

        UserTask::where('user_id','=',$group->user_id)
            ->where('group_id','=',$group->group_id)
            ->delete();

        $group->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除学习小组成员');

        return Redirect::back();
    }

    public function get_group_user_chatable()
    {
        $group = UserGroup::find(\Laravel\Input::get('id'));


        if($group->chatable == 1)
        {
            $group->chatable = 0;
        }
        else
        {
            $group->chatable = 1;
        }
        $group->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'设置学习小组成员群聊属性');

        return Redirect::back();
    }

    public function get_task_list()
    {
        $view = View::make('learn.task_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = new UTask();

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
        $view->web_title = '学习任务管理';
        return $view;
    }

    public function get_task_add()
    {
        $view = View::make('learn.task_add');

        $view->top_types = ProductType::where('status','=',1)->where('level','=',1)->get();

        $view->web_title = '添加学习任务';
        return $view;
    }

    public function post_task_add()
    {
        $rules = array(
            'title'      => 'required|min:2|max:16',
            'id'         => 'required',
            'start_at'   => 'required',
            'end_at'     => 'required',
            'product_id' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $task = new UTask();
        $task->title = Input::get('title');
        $task->products = serialize(Input::get('product_id'));
        $task->group_id = Input::get('id');
        $task->admin_id = Auth::user()->id;
        $task->sms_notify = Input::has('sms_notify') ? 1 : 0;
        $task->push_notify = Input::has('push_notify') ? 1 : 0;
        $task->notify_before = Input::get('notify_before');
        $task->start_at = Input::get('start_at');
        $task->end_at = Input::get('end_at');
        $task->status = Input::has('status') ? 1 : 0;
        $task->save();

        //立即发布
        if(Input::has('status'))
        {
            $task_id = $task->id;
            $group = UserGroup::where('group_id','=',Input::get('id'));

            if($group->count() == 0)
            {
                Messages::add('error','当前学习小组没有成员,不能发布任务!请先添加小组成员!');
                return  Redirect::back()
                    ->with_input();
            }

            foreach($group->get(array('user_id')) as $user)
            {
                $user_id = $user->user_id;

                $user_task = new UserTask();
                $user_task->task_id   = $task_id;
                $user_task->group_id  = Input::get('id');
                $user_task->user_id   = $user_id;
                $user_task->completed = serialize(array());
                $user_task->save();
            }
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加学习任务');

        return Redirect::to('learn/group_list');
    }

    public function get_task_detail()
    {
        $view = View::make('learn.task_detail');

        $task = UTask::find(Input::get('id'));

        $products = unserialize($task->products);
        $view->task = $task;
        $view->products = Product::where_in('id',$products)->get();
        $view->web_title = '学习任务详情 - '.$task->title;
        return $view;
    }

    public function get_task_edit()
    {
        $view = View::make('learn.task_edit');

        $view->top_types = ProductType::where('status','=',1)->where('level','=',1)->get();
        $view->task = UTask::find(Input::get('id'));
        $view->web_title = '编辑学习任务';
        return $view;
    }

    public function post_task_edit()
    {
        $rules = array(
            'title'      => 'required|min:2|max:16',
            'id'         => 'required',
            'start_at'   => 'required',
            'end_at'     => 'required',
            'product_id' => 'required'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $task = UTask::find(Input::get('id'));
        $task->title = Input::get('title');
        $task->products = serialize(Input::get('product_id'));
        $task->sms_notify = Input::has('sms_notify') ? 1 : 0;
        $task->push_notify = Input::has('push_notify') ? 1 : 0;
        $task->notify_before = Input::get('notify_before');
        $task->start_at = Input::get('start_at');
        $task->end_at = Input::get('end_at');
        $task->status = Input::has('status') ? 1 : 0;
        $task->save();

        //立即发布
        if(Input::has('status'))
        {
            $group = UserGroup::where('group_id','=',$task->group_id)->get(array('user_id'));
            foreach($group as $user)
            {
                $user_id = $user->user_id;

                $user_task = new UserTask();
                $user_task->task_id   = Input::get('id');
                $user_task->group_id  = $task->group_id;
                $user_task->user_id   = $user_id;
                $user_task->completed = serialize(array());
                $user_task->save();
            }
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'编辑学习任务');

        return Redirect::to(URL::to(base64_decode(Input::get('ref'))));
    }

    public function get_task_del()
    {
        $user_task = UserTask::where('task_id','=',Input::get('id'));
        $user_task->delete();

        UTask::where('id','=',Input::get('id'))->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除学习任务');

        return Redirect::back();
    }

    public function get_task_status()
    {
        $view = View::make('learn.task_status');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $task = UTask::find(Input::get('id'));

        $group = Group::find($task->group_id);

        if($group == null) return \Laravel\Response::error(404);

        $orm_obj = UserGroup::where('group_id','=',$task->group_id);

        $total = $orm_obj->count();

        $view->task = $task;

        $view->total = $total;

        $view->complete_count = UserTask::where('task_id','=',Input::get('id'))
            ->where('complete_status','=',1)
            ->count();

        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array('id' => Input::get('id')));
        $view->web_title = '学习任务完成情况统计 - '.$task->title;
        return $view;
    }

    public function get_chat_list()
    {
        $view = View::make('learn.chat_list');

        $page = \Laravel\Input::get('page',1);
        $per_page = 30;

        $orm_obj = Chat::where('gid','=',Input::get('id'));

        if(Input::has('name'))
        {
            $orm_obj = $orm_obj->where('name','like','%'.Input::get('name').'%');
        }
        if(Input::has('keyword'))
        {
            $orm_obj = $orm_obj->where('content','like','%'.Input::get('keyword').'%');
        }

        $total = $orm_obj->count();

        $view->list = $orm_obj
            ->order_by('id','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'id'      => Input::get('id'),
                'name'    => Input::get('name'),
                'keyword' => Input::get('keyword')
            ));

        $view->web_title = '群聊记录';
        return $view;
    }

    public function get_chat_del()
    {
        if(Input::has('id'))
        {
            $chat = Chat::find(Input::get('id'));
            $chat->status = 0;
            $chat->save();
        }
        if(Input::has('gid'))
        {
            $chat = Chat::where('gid','=',Input::get('gid'));
            $chat->update(array('status' => 0));
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除群聊记录');

        return Redirect::back();
    }

    public function get_chat_recover()
    {
        if(Input::has('id'))
        {
            $chat = Chat::find(Input::get('id'));
            $chat->status = 1;
            $chat->save();
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'恢复群聊记录');

        return Redirect::back();
    }

}