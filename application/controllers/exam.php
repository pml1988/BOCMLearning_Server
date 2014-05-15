<?php
/**
 * User: wxj
 * Date: 14-5-13
 * Time: 下午4:55
 * File_name: exam.php
 * Email: wxjajax@gmail.com
*/

class Exam_Controller extends Base_Controller {

    public $restful = true;

    public function get_subject_list()
    {
        $view = View::make('exam.subject_list');
        $page = \Laravel\Input::get('page', 1);
        $per_page = 20;

        $pool = new Pool();
        $total = $pool->count();
        $list = $pool
                ->order_by('created_at', 'desc')
                ->take($per_page)
                ->skip(($page-1)*$per_page)
                ->get();

        $view->list = $list;
        $view->web_title = '科目管理';
        $view->page_link = \Laravel\Paginator::make($view->list, $total, $per_page);
        return $view;
    }

    public function get_subject_add()
    {
        $view = View::make('exam.subject_add');
        $view->web_title = '科目添加';
        return $view;
    }

    public function post_subject_add()
    {
        $rules = array(
            'title'  => 'required|max:16|min:2',
        );

        $validation = Validator::make(Input::get(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $data['title'] = \Laravel\Input::get('title');
        $pool = new Pool;
        $pool->title =  $data['title'];
        $pool->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加科目');

        return Redirect::to('exam/subject_list');
    }

    public function get_subject_edit()
    {
        $view = View::make('exam.subject_edit');

        $pool = Pool::find(\Laravel\Input::get('id'));
        $view->pool = $pool;
        $view->web_title = '科目编辑';
        return $view;
    }

    public function post_subject_edit()
    {
        $rules = array(
            'title'  => 'required|max:16|min:2',
        );

        $validation = Validator::make(Input::get(), $rules);

        if ($validation->fails()) {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $data['title'] = \Laravel\Input::get('title');
        $pool = Pool::find(\Laravel\Input::get('id'));
        $pool->title =  $data['title'];
        $pool->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改科目');

        return Redirect::to('exam/subject_list');
    }

    public function get_subject_import()
    {

    }
} 