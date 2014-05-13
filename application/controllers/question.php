<?php
/**
 * Author: RaymondChou
 * Date: 12-12-25
 * File: question.php
 * Email: zhouyt.kai7@gmail.com
 */
class Question_Controller extends Base_Controller {
    
    public $restful = true;

    public function get_type_list()
    {
        $view = View::make('question.type_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $total = QuestionType::where_in('id',Util::role_describe('question'))->count();
        }
        else
        {
            $total = QuestionType::count();
        }

        $list = QuestionType::order_by('sort','asc');

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $list = $list->where_in('id',Util::role_describe('question'));
        }

        $view->list = $list
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '问答分类管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page);

        return $view;
    }

    public function get_type_add()
    {
        return View::make('question.type_add')
            ->with(array('web_title' => '添加问答分类'));
    }

    public function post_type_add()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
            'sort'  => 'required|integer'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = new QuestionType();
        $type->name = Input::get('name');
        $type->sort = Input::get('sort');
        $type->status = Input::has('status') ? 1 : 0;
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加问答分类');

        return Redirect::to('question/type_list');
    }

    public function get_type_edit()
    {
        $type = QuestionType::find(Input::get('id'));

        if($type == null)
            return \Laravel\Response::error(404);

        return View::make('question.type_edit')
            ->with('web_title','编辑问答分类')
            ->with('type',$type);
    }

    public function post_type_edit()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
            'sort'  => 'required|integer'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = QuestionType::find(Input::get('id'));
        $type->name = Input::get('name');
        $type->sort = Input::get('sort');
        $type->status = Input::has('status') ? 1 : 0;
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改问答分类');

        return Redirect::to('question/type_list');
    }

    public function get_type_del()
    {
        $type = QuestionType::find(\Laravel\Input::get('id'));
        $type->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除问答分类');

        return Redirect::back();
    }

    public function get_question_list()
    {
        $view = View::make('question/question_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = Question::with('question_type');

        if(Input::has('search_name'))
        {
            $orm_obj = $orm_obj->where('title','like','%'.Input::get('search_name').'%');
        }
        if(Input::has('search_type'))
        {
            $orm_obj = $orm_obj->where('question_type_id','=',Input::get('search_type'));
        }
        if(Input::has('search_start'))
        {
            $orm_obj = $orm_obj->where('created_at','>=',Input::get('search_start'));
        }
        if(Input::has('search_end'))
        {
            $orm_obj = $orm_obj->where('created_at','<=',date('Y-m-d',strtotime(Input::get('search_end'))+3600*24));
        }

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $orm_obj = $orm_obj->where_in('question_type_id',Util::role_describe('question'));
        }
        $total = $orm_obj->count();

        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $question_types = QuestionType::where_in('id',Util::role_describe('question'))->get();
        }
        else
        {
            $question_types = QuestionType::all();
        }

        $view->web_title = '问答管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'search_name'  => \Laravel\Input::get('search_name'),
                'search_type'  => \Laravel\Input::get('search_type'),
                'search_start' => \Laravel\Input::get('search_start'),
                'search_end'   => \Laravel\Input::get('search_end')
            ));

        $view->types = $question_types;
        return $view;
    }

    public function get_question_edit()
    {
        $question = Question::find(Input::get('id'));

        if($question == null)
            return \Laravel\Response::error(404);

        $question_types = QuestionType::all();

        return View::make('question.question_edit')
            ->with('web_title','编辑问答')
            ->with('question',$question)
            ->with('question_types',$question_types);
    }

    public function post_question_edit()
    {
        $rules = array(
            'question_type_id' => 'required',
            'content'          => 'required|max:256|min:2',
            'title'            => 'required|min:2|max:32'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $question = Question::find(Input::get('id'));
        $question->title            = Input::get('title');
        $question->content          = Input::get('content');
        $question->question_type_id = Input::get('question_type_id');
        $question->is_suggest       = Input::has('is_suggest') ? 1 : 0;
        $question->can_answer       = Input::has('can_answer') ? 1 : 0;
        $question->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改问题');

        return Redirect::to(URL::to(base64_decode(Input::get('ref'))));
    }

    public function get_question_del()
    {
        $question = Question::find(\Laravel\Input::get('id'));
        $question->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除问题');

        return Redirect::back();
    }

    public function get_question_suggest()
    {
        $question = Question::find(\Laravel\Input::get('id'));
        if($question->is_suggest == 1)
            $question->is_suggest = 0;
        else
            $question->is_suggest = 1;
        $question->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'设置问答推荐');

        return Redirect::back();
    }

    public function get_answer_list()
    {
        $question = Question::find(Input::get('id'));

        if($question == null)
            return \Laravel\Response::error(404);

        $view = View::make('question.answer_list');

        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $total = $question->answer()->count();

        $view->list = $question->answer()
            ->order_by('is_best','desc')
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '问题回答';
        $view->question = $question;
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'id'  => \Laravel\Input::get('id'),
                'ref' => Input::get('ref')
            ));;

        return $view;
    }

    public function get_answer_best()
    {
        $answer = Answer::find(\Laravel\Input::get('id'));
        if($answer->is_best == 1)
        {
            $answer->is_best = 0;
        }
        else
        {
            $answer->is_best = 1;

            //积分变更
            Util::best_answer_score_plus($answer->user_id);
        }
        $answer->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'设置最佳答案');

        return Redirect::back();
    }

    public function get_answer_del()
    {
        $answer = Answer::find(\Laravel\Input::get('id'));
        $answer->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除问题回答');

        return Redirect::back();
    }

    public function get_answer_edit()
    {
        $answer = Answer::find(Input::get('id'));

        if($answer == null)
            return \Laravel\Response::error(404);

        return View::make('question.answer_edit')
            ->with('web_title','编辑回答')
            ->with('answer',$answer);
    }

    public function post_answer_edit()
    {
        $rules = array(
            'content'          => 'required|max:1000|min:2'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $answer = Answer::find(Input::get('id'));
        $answer->content = Input::get('content');
        $answer->is_best = Input::has('is_best') ? 1 : 0;
        $answer->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改问题回答');

        return Redirect::to(URL::to(base64_decode(Input::get('ref'))));
    }
}