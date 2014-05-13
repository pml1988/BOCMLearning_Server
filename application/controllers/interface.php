<?php
/**
 * Author: RaymondChou
 * Date: 12-12-20
 * File: interface.php
 * Email: zhouyt.kai7@gmail.com
 */

class Interface_Controller extends Base_Controller {

    public $restful = true;

    //获取全部产品分类
    public function get_all_product_types()
    {
        $validation = static::validation(array(
            'user_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $top_types = ProductType::where('level','=',1)
            ->where('status','=',1)
            ->order_by('sort','asc')
            ->get(array('id as parent_id','name'));
       //构造子父系输出结构
        foreach($top_types as $top_type)
        {
            $build_data = $top_type->to_array();
            $children = ProductType::where('top_id','=',$build_data['parent_id'])
                ->where('status','=',1)
                ->order_by('sort','asc')
                ->get(array('id','name'));

            //构造子目录结构
            $build_data['children'] = array();
            foreach($children as $child)
            {
                $children_data = $child->to_array();

                $children_data['has_interested'] =
                    Interest::where('user_id','=',Input::get('user_id'))
                    ->where('product_type_id','=',$children_data['id'])
                    ->where('status','=',1)
                    ->count() == 1 ? true : false;

                $build_data['children'][] = $children_data;

            }
            $data[] = $build_data;
        }

        return static::response_data($data, 200, 'array');
    }

    //获取全部问答分类
    public function get_all_question_types()
    {
        $validation = static::validation(array(
            'user_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $question_types = QuestionType::where('status','=',1)->get(array('id', 'name'));
        return static::response_data($question_types, 200, 'eloquent');
    }

    //获取产品列表接口
    public function get_product_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'method'  => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $method = Input::get('method');
        $orm_obj = Product::with('product_type')->where('status','=',1);
        $status = false;
        //判断请求类型
        switch($method)
        {
            case 'suggest':
                $orm_obj = $orm_obj->where('is_suggest','=',1);
                $status = true;
                break;
            case 'new':
                $orm_obj = $orm_obj->order_by('created_at','desc');
                $status = true;
                break;
            case 'hot':
                $orm_obj = $orm_obj->order_by('hot','desc');
                $status = true;
                break;
            case 'type':
                if(Input::has('type_id'))
                {
                    $orm_obj = $orm_obj
                        ->where_product_type_id(Input::get('type_id'));
                    $status = true;
                }
                break;
            case 'search':
                if(Input::has('keyword'))
                {
                    $orm_obj = $orm_obj
                        ->where('product_name','like','%'.Input::get('keyword').'%');
                    $status = true;
                }
                break;
        }
        //判断类型及参数是否正确
        if($status === true)
        {
            $data['per_page']   = 20;
            $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
            $data['list']       = [];

            $orm_obj = $orm_obj
                ->order_by('sort','asc')
                ->take($data['per_page'])
                ->skip((Input::get('page') - 1)*$data['per_page'])
                ->get();
            //构造输出
            foreach($orm_obj as $product)
            {
                $row['id']           = $product->id;
                $row['name']         = $product->product_name;
                $row['info']         = $product->info;
                $row['product_type'] = $product->product_type->name;
                $row['hot']          = (int)$product->hot;
                $row['comment_count']= (int)$product->comment_count;
                $row['avg_score']    = ceil($product->product_comment()->where('score','!=',0)->avg('score'));

                $data['list'][] = $row;
            }
            return static::response_data($data);
        }
        else
        {
            return static::response_error('attribute error');
        }
    }

    //获取产品详情接口
    public function get_product_detail()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'product_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $product = Product::find(Input::get('product_id'));

        if($product == null)
        {
            return static::response_error('product not exist');
        }

        $data                  = $product->to_array();
        $data['video_image_url'] = $product->video_image_url;
        $data['comment_count'] = (int)$product->product_comment()->count();
        $data['avg_score']     = ceil($product->product_comment()->where('score','!=',0)->avg('score'));
        $data['has_collected'] = Collection::where('user_id','=',Input::get('user_id'))
            ->where('item_id','=',Input::get('product_id'))
            ->where('type','=',1)
            ->where('status','=',1)
            ->count() == 1 ? true : false;

        $data['attributes']    = [];
        $data['images']        = [];
        $data['relatives']     = [];
        //详情参数
        $attributes = $product->product_attribute()
            ->order_by('sort','asc')
            ->get(array('value','display'));
        foreach($attributes as $attribute)
        {
            $attribute_data['title']   = $attribute->name;
            $attribute_data['content'] = $attribute->pivot->value;
            $attribute_data['display'] = $attribute->pivot->display == 1 ? true : false;
            $data['attributes'][] = $attribute_data;
        }
        //图片
        $images = $product->image()->get(array('url'));
        foreach($images as $image)
        {
            $image_data['small_image_url']   = $image->small_image_url;
            $image_data['big_image_url']   = $image->url;
            $data['images'][] = $image_data;
        }
        //关联产品
        $relatives = $product->product_tag()->get(array('id'));
        foreach($relatives as $relative)
        {
            $relative_id[]   = $relative->id;
        }
        if($relatives != null)
        {
            $relative_products = DB::table('product_tag_joins')
                ->join('products', 'product_tag_joins.product_id', '=', 'products.id')
                ->where_in('product_tag_joins.product_tag_id',$relative_id)
                ->where_not_in('products.id',array(Input::get('product_id')))
                ->order_by('products.hot','desc')
                ->take(5)
                ->distinct()
                ->get(array('product_id','product_name'));

            foreach($relative_products as $relative_product)
            {
                $data['relatives'][] = $relative_product;
            }
        }

        $product->hot = $product->hot+1;
        $product->save();

        return static::response_data($data);
    }

    //登陆验证接口
    public function post_login()
    {
        $validation = static::validation(array(
            'job_code'   => 'required',
            'password'   => 'required'
        ));
        if($validation !== true) return $validation;

        if(strlen(base64_decode(Input::get('job_code'))) < 7)
            $user = User::where_job_code(base64_decode(Input::get('job_code')))
                ->where_password(strtoupper(md5(base64_decode(Input::get('password')))))
                ->first(array('id as user_id','user_name','job_code','avatar_url','status'));
        else
            $user = User::where_ehr_id(base64_decode(Input::get('job_code')))
                ->where_password(strtoupper(md5(base64_decode(Input::get('password')))))
                ->first(array('id as user_id','user_name','job_code','avatar_url','status'));

        if($user != null)
        {
            if($user->status != 0)
            {
                return static::response_error('user status error', 11);
            }
            else
            {
                //积分变更
                Util::user_login_score_plus($user->user_id);
                return static::response_data($user->to_array());
            }
        }
        else
        {
            return static::response_error('password or job_code error', 10);
        }
    }

    //手机验证接口
    public function post_sms_send()
    {
        $validation = static::validation(array(
            'phone' => 'required'
        ));
        if($validation !== true) return $validation;

        //判断是否允许短信登录
        if(Util::get_setting('sms_enable') != 1)
        {
            return static::response_error('sms unable', 13);
        }

        $user = User::where_phone(base64_decode(Input::get('phone')))
            ->first();

        if($user != null)
        {
            if($user->status != 0)
            {
                return static::response_error('user status error', 11);
            }
            else
            {
                $check_code = mt_rand(10000,99999);
                $sms_content = '您的中行知识管理平台手机登录验证码为:'.$check_code;

                if(Sms::send($user->phone, $sms_content))
                {
                    $user->check_code = $check_code;
                    $user->save();
                    return static::response_data();
                }
                else
                    return static::response_error('sms unable', 13);
            }
        }
        else
        {
            return static::response_error('phone not exist', 12);
        }
    }

    public function post_phone_login()
    {
        $validation = static::validation(array(
            'phone'        => 'required',
            'check_code'   => 'required'
        ));
        if($validation !== true) return $validation;

        $user = User::where_phone(base64_decode(Input::get('phone')))
            ->where_check_code(base64_decode(Input::get('check_code')))
            ->first(array('id as user_id','user_name','job_code','avatar_url','status'));

        if($user != null)
        {
            if($user->status != 0)
            {
                return static::response_error('user status error', 11);
            }
            else
            {
                //积分变更
                Util::user_login_score_plus($user->user_id);
                return static::response_data($user->to_array());
            }
        }
        else
        {
            return static::response_error('phone or check_code error', 14);
        }
    }

    //用户间消息发送接口
    public function post_send_message()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'to_user_id' => 'required|different:user_id',
            'content'    => 'required|min:2|max:256'
        ));
        if($validation !== true) return $validation;

        $message = new Message();
        $message->user_id    = Input::get('user_id');
        $message->to_user_id = Input::get('to_user_id');
        $message->content    = Input::get('content');
        $message->status     = 1;
        $message->save();

        $msg_content = array(
            'n_title'   => '您有一条新消息  江苏中行 M-Learning',
            'n_content' => '您有一条新消息 江苏中行 M-Learning 点击查看',
            'n_extras'  => array(
                'action' => 'new_message',
                'ios'    => array('badge' => 1)
            )
        );

        Bundle::start('jpush');
        $jpush = new Jpush();
        $jpush->send(1234, 3, Input::get('to_user_id'), 1, json_encode($msg_content));

        return static::response_data(null);
    }

    //修改用户头像接口
    public function post_change_avatar()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'avatar'     => 'required'
        ));
        if($validation !== true) return $validation;

        $file_name = time().mt_rand(100,999);
        $file_path = path('public') . '/upload/avatar/'.$file_name.'.png';
        $file = fopen($file_path, "w");
        fwrite($file,base64_decode(Input::get('avatar')));
        fclose($file);

        $user = User::find(Input::get('user_id'));
        $user->avatar_url = '/upload/avatar/'.$file_name.'.png';
        $user->save();

        return static::response_data(array('avatar_url' => URL::to('upload/avatar/'.$file_name.'.png')));
    }

    //收藏取消收藏产品接口
    public function post_product_collection()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'product_id' => 'required',
            'method'     => 'required'
        ));
        if($validation !== true) return $validation;

        $product_collection = Collection::where('item_id','=',Input::get('product_id'))
            ->where('type','=',1)
            ->where('user_id','=',Input::get('user_id'))
            ->first();

        if(Input::get('method') == 'collect')
        {
            if($product_collection == null)
            {
                $collection = new Collection();
                $collection->item_id = Input::get('product_id');
                $collection->type    = 1;
                $collection->user_id = Input::get('user_id');
                $collection->status  = 1;
                $collection->save();
            }
            else
            {
                $product_collection->status = 1;
                $product_collection->save();
            }
            return static::response_data();
        }
        elseif(Input::get('method') == 'uncollect')
        {
            if($product_collection == null)
            {
                $collection = new Collection();
                $collection->item_id = Input::get('product_id');
                $collection->type    = 1;
                $collection->user_id = Input::get('user_id');
                $collection->status  = 0;
                $collection->save();
            }
            else
            {
                $product_collection->status = 0;
                $product_collection->save();
            }
            return static::response_data();
        }
        else
        {
            return static::response_error('method not exist');
        }

    }

    //获取产品评论列表接口
    public function get_product_comment_list()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'product_id' => 'required',
            'page'       => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $comment_list = ProductComment::with('user')
            ->where('product_id','=',Input::get('product_id'));

        $data['per_page']   = 20;
        $data['total_page'] = ceil($comment_list->count()/$data['per_page']);
        $data['list']       = [];

        $comment_list = $comment_list
            ->order_by('is_top','desc')
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        foreach($comment_list as $comment)
        {
            $row['content']    = $comment->content;
            $row['score']      = $comment->score;
            $row['created_at'] = $comment->created_at;
            $row['user_id']    = $comment->user->id;
            $row['user_name']  = $comment->user->user_name;
            $row['avatar_url'] = $comment->user->avatar_url;

            $data['list'][] = $row;
        }

        return static::response_data($data);
    }

    //发布产品评论接口
    public function post_product_comment_submit()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'product_id' => 'required',
            'content'    => 'required|max:256|min:2',
            'score'      => 'required|integer|min:0|max:5'
        ));
        if($validation !== true) return $validation;

        $comment = new ProductComment();
        $comment->user_id    = Input::get('user_id');
        $comment->content    = Input::get('content');
        $comment->product_id = Input::get('product_id');
        $comment->score      = Input::get('score');
        $comment->save();

        //积分变更
        Util::comment_submit_score_plus(Input::get('user_id'));

        return static::response_data();
    }

    //获取问答列表接口
    public function get_question_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'method'  => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $method = Input::get('method');
        $orm_obj = Question::with(array('question_type','user'))->where('status','=',1);
        $status = false;
        //判断请求类型
        switch($method)
        {
            case 'suggest':
                $orm_obj = $orm_obj->where('is_suggest','=',1);
                $status = true;
                break;
            case 'new':
                $orm_obj = $orm_obj->order_by('created_at','desc');
                $status = true;
                break;
            case 'hot':
                $orm_obj = $orm_obj->order_by('hot','desc');
                $status = true;
                break;
            case 'type':
                if(Input::has('type_id'))
                {
                    $orm_obj = $orm_obj
                        ->where_question_type_id(Input::get('type_id'));
                    $status = true;
                }
                break;
            case 'search':
                if(Input::has('keyword'))
                {
                    $orm_obj = $orm_obj
                        ->where('title','like','%'.Input::get('keyword').'%');
                    $status = true;
                }
                break;
        }
        //判断类型及参数是否正确
        if($status === true)
        {
            $data['per_page']   = 20;
            $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
            $data['list']       = [];

            $orm_obj = $orm_obj
                ->order_by('is_top','desc')
                ->take($data['per_page'])
                ->skip((Input::get('page') - 1)*$data['per_page'])
                ->get();
            //构造输出
            foreach($orm_obj as $question)
            {
                $row['id']            = $question->id;
                $row['title']         = $question->title;
                $row['content']       = $question->content;
                $row['created_at']    = $question->created_at;
                $row['question_type'] = $question->question_type->name;
                $row['answer_count']  = Answer::where('question_id','=',$question->id)->count();
                $row['user_id']       = $question->user->id;
                $row['user_name']     = $question->user->user_name;
                $row['avatar_url']    = $question->user->avatar_url;
                $row['can_answer']    = $question->can_answer == 1 ? true : false;
                $row['has_collected'] = Collection::where('user_id','=',Input::get('user_id'))
                    ->where('item_id','=',$question->id)
                    ->where('type','=',2)
                    ->where('status','=',1)
                    ->count() == 1 ? true : false;

                $data['list'][] = $row;
            }
            return static::response_data($data);
        }
        else
        {
            return static::response_error('attribute error');
        }
    }

    //发布问题接口
    public function post_question_submit()
    {
        $validation = static::validation(array(
            'user_id'          => 'required',
            'question_type_id' => 'required',
            'content'          => 'required|max:256|min:2',
            'title'            => 'required|min:2|max:32'
        ));
        if($validation !== true) return $validation;

        $question = new Question();
        $question->user_id          = Input::get('user_id');
        $question->content          = Input::get('content');
        $question->question_type_id = Input::get('question_type_id');
        $question->title            = Input::get('title');
        $question->save();

        //积分变更
        Util::question_submit_score_plus(Input::get('user_id'));

        return static::response_data();
    }

    //获取问题详情接口
    public function get_question_detail()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'question_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $question = Question::with('user')->find(Input::get('question_id'));

        if($question == null)
        {
            return static::response_error('question not exist');
        }

        $data['id']            = $question->id;
        $data['title']         = $question->title;
        $data['content']       = $question->content;
        $data['created_at']    = $question->created_at;
        $data['can_answer']    = $question->can_answer == 1 ? true : false;
        $data['answer_count']  = Answer::where('question_id','=',$question->id)->count();
        $data['user_id']       = $question->user->id;
        $data['user_name']     = $question->user->user_name;
        $data['avatar_url']    = $question->user->avatar_url;
        $data['has_collected'] = Collection::where('user_id','=',Input::get('user_id'))
            ->where('item_id','=',Input::get('question_id'))
            ->where('type','=',2)
            ->where('status','=',1)
            ->count() == 1 ? true : false;

        $question->hot = $question->hot+1;
        $question->save();

        return static::response_data($data);
    }

    //获取回答列表接口
    public function get_answer_list()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'question_id' => 'required',
            'page'        => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Answer::with(array('user'))->where('question_id','=',Input::get('question_id'));

        $data['per_page']   = 10;
            $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
            $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('is_best','desc')
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        foreach($orm_obj as $answer)
        {
            $row['content']    = $answer->content;
            $row['created_at'] = $answer->created_at;
            $row['user_id']    = $answer->user->id;
            $row['user_name']  = $answer->user->user_name;
            $row['avatar_url'] = $answer->user->avatar_url;
            $row['is_best']    = $answer->is_best == 1 ? true : false;

            $data['list'][] = $row;
        }

        if(\Laravel\Input::get('page') == 1)
        {
            $question = Question::find(Input::get('question_id'));
            $question->hot = $question->hot+1;
            $question->save();
        }
        return static::response_data($data);
    }

    //收藏取消收藏问题接口
    public function post_question_collection()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'question_id' => 'required',
            'method'      => 'required'
        ));
        if($validation !== true) return $validation;

        $product_collection = Collection::where('item_id','=',Input::get('question_id'))
            ->where('type','=',2)
            ->where('user_id','=',Input::get('user_id'))
            ->first();

        if(Input::get('method') == 'collect')
        {
            if($product_collection == null)
            {
                $collection = new Collection();
                $collection->item_id = Input::get('question_id');
                $collection->type    = 2;
                $collection->user_id = Input::get('user_id');
                $collection->status  = 1;
                $collection->save();
            }
            else
            {
                $product_collection->status = 1;
                $product_collection->save();
            }
            return static::response_data();
        }
        elseif(Input::get('method') == 'uncollect')
        {
            if($product_collection == null)
            {
                $collection = new Collection();
                $collection->item_id = Input::get('question_id');
                $collection->type    = 2;
                $collection->user_id = Input::get('user_id');
                $collection->status  = 0;
                $collection->save();
            }
            else
            {
                $product_collection->status = 0;
                $product_collection->save();
            }
            return static::response_data();
        }
        else
        {
            return static::response_error('method not exist');
        }

    }

    //回答问题接口
    public function post_answer_submit()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'question_id' => 'required',
            'content'     => 'required|max:1000|min:2',
        ));
        if($validation !== true) return $validation;

        $answer = new Answer();
        $answer->user_id     = Input::get('user_id');
        $answer->content     = Input::get('content');
        $answer->question_id = Input::get('question_id');
        $answer->save();

        //积分变更
        Util::answer_submit_score_plus(Input::get('user_id'));

        return static::response_data();
    }

    //获取用户信息接口
    public function get_user_info()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
        ));
        if($validation !== true) return $validation;

        $user = User::find(Input::get('user_id'));

        if($user == null)
            return static::response_error('user not exist');

        $data['user_id']    = $user->id;
        $data['user_name']  = $user->user_name;
        $data['avatar_url'] = $user->avatar_url;
        $data['job_code']   = $user->job_code;
        $data['score']      = $user->score;
        $data['level']      = $user->level;
        $data['bank']       = $user->bank_name;
        $data['new_message']= Message::where('to_user_id','=',$user->id)
            ->where('status','=',1)->count();

        return static::response_data($data);
    }

    //获取消息列表接口
    public function get_message_list()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'page'        => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Message::with(array('user'))->where('to_user_id','=',Input::get('user_id'));

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        foreach($orm_obj as $message)
        {
            $row['message_id'] = $message->id;
            $row['content']    = Helper::ellipsize_cn($message->content,16,1,'UTF-8','...');;
            $row['created_at'] = $message->created_at;
            $row['status']     = $message->status == 1 ? true : false;
            $row['is_system']  = $message->is_system == 1 ? true : false;
            $row['user_id']    = $message->is_system == 1 ? null : $message->user->id;
            $row['user_name']  = $message->is_system == 1 ? '系统' : $message->user->user_name;
            $row['avatar_url'] = $message->is_system == 1 ? URL::to('img/boc.png') : $message->user->avatar_url;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //获取消息详情接口
    public function get_message_detail()
    {
        $validation = static::validation(array(
            'user_id'     => 'required',
            'message_id'  => 'required'
        ));
        if($validation !== true) return $validation;

        $message = Message::find(Input::get('message_id'));

        if($message == null)
            return static::response_error('message not exist');

        $message->status = 0;
        $message->save();

        return static::response_data(array('content' => $message->content));
    }

    //获取个人问答列表接口
    public function get_my_question_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Question::with(array('question_type'))
            ->where('user_id','=',Input::get('user_id'))
            ->where('status','=',1);

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $question)
        {
            $row['id']            = $question->id;
            $row['title']         = $question->title;
            $row['content']       = $question->content;
            $row['created_at']    = $question->created_at;
            $row['question_type'] = $question->question_type->name;
            $row['answer_count']  = Answer::where('question_id','=',$question->id)->count();
            $row['user_id']       = $question->user->id;
            $row['user_name']     = $question->user->user_name;
            $row['avatar_url']    = $question->user->avatar_url;
            $row['can_answer']    = $question->can_answer == 1 ? true : false;
            $row['has_collected'] = Collection::where('user_id','=',Input::get('user_id'))
                ->where('item_id','=',$question->id)
                ->where('type','=',2)
                ->where('status','=',1)
                ->count() == 1 ? true : false;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //获取个人收藏列表接口
    public function get_my_collection_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Collection::where('user_id','=',Input::get('user_id'))
            ->where('status','=',1);

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('updated_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $collection)
        {
            $row['id']            = $collection->item_id;
            $collect_data = $collection->type == 1 ? Product::find($collection->item_id)
                                                   : Question::find($collection->item_id);
            if($collect_data == null)
                continue;
            $row['title'] = $collection->type == 1 ? $collect_data->product_name
                                                   : $collect_data->title;
            $row['content'] = $collection->type == 1 ? $collect_data->info
                                                     : $collect_data->content;
            $row['updated_at'] = $collection->updated_at;
            $row['type'] = $collection->type == 1 ? 'product' : 'question';

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //删除消息接口
    public function post_message_delete()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'message_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $messages = explode(',',Input::get('message_id'));
        $messages_obj = Message::where_in('id',$messages);
        $messages_obj->delete();
        return static::response_data();
    }

    //获取我的兴趣列表
    public function get_my_interest_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Interest::with('product_type')
            ->where('user_id','=',Input::get('user_id'))
            ->where('status','=',1);

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('updated_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $interest)
        {
            if($interest->product_type == null)
                continue;
            $row['product_type_id'] = $interest->product_type_id;
            $row['type_name']       = $interest->product_type->name;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //添加我的兴趣接口
    public function post_interest_add()
    {
        $validation = static::validation(array(
            'user_id'         => 'required',
            'product_type_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $interests = explode(',',Input::get('product_type_id'));

        foreach($interests as $interest)
        {
            $db_had = Interest::where('user_id','=',Input::get('user_id'))
                ->where('product_type_id','=',$interest)->first();
            if($db_had == null)
            {
                Interest::create(array(
                    'product_type_id' => $interest,
                    'user_id'         => Input::get('user_id')
                ));
            }
            else
            {
                $db_had->status = 1;
                $db_had->save();
            }
        }
        return static::response_data();
    }

    //删除我的兴趣接口
    public function post_interest_delete()
    {
        $validation = static::validation(array(
            'user_id'         => 'required',
            'product_type_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $interests = explode(',',Input::get('product_type_id'));

        foreach($interests as $interest)
        {
            $db_had = Interest::where('user_id','=',Input::get('user_id'))
                ->where('product_type_id','=',$interest)->first();
            if($db_had != null)
            {
                $db_had->status = 0;
                $db_had->save();
            }
        }
        return static::response_data();
    }

    //获取全部小组列表接口
    public function get_group_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = new Group();

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $group)
        {
            $row['id']         = $group->id;
            $row['name']       = $group->name;
            $row['detail']     = $group->detail;
            $row['icon_url']   = $group->icon_url;
            $row['has_joined'] = UserGroup::where('user_id','=',Input::get('user_id'))
                    ->where('group_id','=',$group->id)
                    ->count() > 0 ? true : false;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //搜索小组接口
    public function get_group_search()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'keyword' => 'required'
        ));
        if($validation !== true) return $validation;

        $orm_obj = new Group();

        $orm_obj = $orm_obj
            ->where('num','=',Input::get('keyword'))
            ->or_where('admin_name','=',Input::get('keyword'))
            ->or_where('name','=',Input::get('keyword'))
            ->order_by('created_at','desc')
            ->get();

        $data = [];

        //构造输出
        foreach($orm_obj as $group)
        {
            $row['id']         = $group->id;
            $row['name']       = $group->name;
            $row['detail']     = $group->detail;
            $row['icon_url']   = $group->icon_url;
            $row['has_joined'] = UserGroup::where('user_id','=',Input::get('user_id'))
                ->where('group_id','=',$group->id)
                ->count() > 0 ? true : false;

            $data[] = $row;
        }

        return static::response_data($data);
    }

    //获取我加入的小组列表接口
    public function get_my_group_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = UserGroup::with('group')
            ->where('user_id','=',Input::get('user_id'));

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $group)
        {
            $row['id']         = $group->group->id;
            $row['name']       = $group->group->name;
            $row['detail']     = $group->group->detail;
            $row['icon_url']   = $group->group->icon_url;
            $row['has_joined'] = UserGroup::where('user_id','=',Input::get('user_id'))
                ->where('group_id','=',$group->group->id)
                ->count() > 0 ? true : false;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //获取学习小组详情接口
    public function get_group_detail()
    {
        $validation = static::validation(array(
            'user_id'  => 'required',
            'group_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $orm_obj = Group::where('id','=',Input::get('group_id'))->first();

        $data['id']       = $orm_obj->id;
        $data['num']      = $orm_obj->num;
        $data['name']     = $orm_obj->name;
        $data['detail']   = $orm_obj->detail;
        $data['icon_url'] = $orm_obj->icon_url;
        $data['admin']    = User::find($orm_obj->admin_id)->user_name;

        $user_group = UserGroup::where('user_id','=',Input::get('user_id'))
            ->where('group_id','=',$orm_obj->id)->first();

        $data['has_joined'] = $user_group != null ? true : false;

        if($data['has_joined'] === true)
        {
            $data['chatable']   = $user_group->chatable == 1 ? true : false;
            $data['is_freedom'] = $user_group->is_freedom == 1 ? true : false;
        }
        else
        {
            $data['chatable']   = false;
            $data['is_freedom'] = false;
        }

        return static::response_data($data);
    }

    //加入小组接口
    public function post_group_join()
    {
        $validation = static::validation(array(
            'user_id'  => 'required',
            'group_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $joined = UserGroup::where('user_id','=',Input::get('user_id'))
            ->where('group_id','=',Input::get('group_id'))
            ->count();

        if($joined > 0)
        {
            return static::response_error('had joined',20);
        }
        else
        {
            $join = new UserGroup();
            $join->user_id     = Input::get('user_id');
            $join->group_id    = Input::get('group_id');
            $join->chatable    = 0;
            $join->is_freedom  = 1;
            $join->save();

            $tasks = UTask::where('group_id','=',Input::get('group_id'))
                ->where('status','=',1);

            if($tasks->count() > 0)
            {
                //有已发布的任务,则继承任务
                $tasks = $tasks->get();

                foreach($tasks as $task)
                {
                    $user_task = new UserTask();
                    $user_task->task_id   = $task->id;
                    $user_task->group_id  = $task->group_id;
                    $user_task->user_id   = Input::get('user_id');
                    $user_task->completed = serialize(array());
                    $user_task->save();
                }
            }

            return static::response_data();
        }
    }

    //退出小组接口
    public function post_group_leave()
    {
        $validation = static::validation(array(
            'user_id'  => 'required',
            'group_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $joined = UserGroup::where('user_id','=',Input::get('user_id'))
            ->where('group_id','=',Input::get('group_id'));

        if($joined->count() == 0)
        {
            return static::response_error('had not joined',21);
        }
        else
        {

            if($joined->first()->is_freedom == 1)
            {
                $joined->delete();

                //删除相关参与的任务
                UserTask::where('user_id','=',Input::get('user_id'))
                    ->where('group_id','=',Input::get('group_id'))
                    ->delete();

                return static::response_data();
            }
            else
            {
                return static::response_error('not freedom',22);
            }
        }
    }

    //获取小组成员接口
    public function get_group_member()
    {
        $validation = static::validation(array(
            'user_id'  => 'required',
            'group_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $user_group = UserGroup::where('group_id','=',Input::get('group_id'))
            ->get();

        $data = array();

        foreach($user_group as $user)
        {
            $row['user_id']    = $user->user_id;
            $row['user_name']  = $user->user->user_name;
            $row['avatar_url'] = $user->user->avatar_url;
            $row['department'] = $user->user->post;
            $data[] = $row;
        }

        return static::response_data($data);
    }

    //获取我的任务列表接口
    public function get_group_task_list()
    {
        $validation = static::validation(array(
            'user_id'  => 'required',
            'group_id' => 'required',
            'page'     => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = UserTask::with(array('utask' => function($query)
        {
            $query->where('start_at', '<=', date('Y-m-d'));
            $query->where('end_at', '>=', date('Y-m-d'));
        },'group'))
            ->where('group_id','=',Input::get('group_id'))
            ->where('user_id','=',Input::get('user_id'));

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $task)
        {
            if($task->utask == null) continue;
            $row['task_id']       = $task->task_id;
            $row['title']         = $task->utask->title;
            $row['group']         = $task->group->name;
            $row['has_completed'] = $task->complete_status == 1 ? true : false;
            $row['created_at']    = $task->utask->created_at;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //获取我的任务列表接口
    public function get_my_task_list()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'page'    => 'required|integer|min:1'
        ));
        if($validation !== true) return $validation;

        $orm_obj = UserTask::with(array('utask' => function($query)
        {
            $query->where('start_at', '<=', date('Y-m-d'));
            $query->where('end_at', '>=', date('Y-m-d'));
        },'group'))
            ->where('user_id','=',Input::get('user_id'));

        $data['per_page']   = 20;
        $data['total_page'] = ceil($orm_obj->count()/$data['per_page']);
        $data['list']       = [];

        $orm_obj = $orm_obj
            ->order_by('created_at','desc')
            ->take($data['per_page'])
            ->skip((Input::get('page') - 1)*$data['per_page'])
            ->get();

        //构造输出
        foreach($orm_obj as $task)
        {
            if($task->utask == null) continue;
            $row['task_id']       = $task->task_id;
            $row['title']         = $task->utask->title;
            $row['group']         = $task->group->name;
            $row['has_completed'] = $task->complete_status == 1 ? true : false;
            $row['created_at']    = $task->utask->created_at;

            $data['list'][] = $row;
        }
        return static::response_data($data);
    }

    //获取任务详情接口
    public function get_my_task_detail()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'task_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $task = UserTask::with('utask')
            ->where('task_id','=',Input::get('task_id'))
            ->where('user_id','=',Input::get('user_id'))
            ->first();

        $completed    = unserialize($task->completed);
        $product_list = unserialize($task->utask->products);
        foreach($product_list as $product_id)
        {
            $product = Product::with('product_type')
                ->where('id','=',$product_id)
                ->first(array('id','info','product_type_id','product_name'));

            if($product == null) continue;
            $row['id']           = $product->id;
            $row['name']         = $product->product_name;
            $row['info']         = $product->info;
            $row['product_type'] = $product->product_type->name;
            $row['has_completed'] = in_array($product_id,$completed) ? true : false;
            $row['hot']          = (int)$product->hot;
            $row['comment_count']= (int)$product->comment_count;
            $row['avg_score']    = ceil($product->product_comment()->where('score','!=',0)->avg('score'));

            $data[] = $row;
        }
        return static::response_data($data);
    }

    //完成任务中某产品接口
    public function post_complete_task()
    {
        $validation = static::validation(array(
            'user_id'    => 'required',
            'task_id'    => 'required',
            'product_id' => 'required'
        ));
        if($validation !== true) return $validation;

        $task = UserTask::
            where('task_id','=',Input::get('task_id'))
            ->where('user_id','=',Input::get('user_id'))
            ->first();

        $completed = unserialize($task->completed);

        if(in_array(Input::get('product_id'),$completed))
        {
            return static::response_error('has completed',400);
        }
        else
        {
            array_push($completed,Input::get('product_id'));

            //修改已完成状态
            if(count($completed) >= count(unserialize($task->utask->products)))
            {
                $task->complete_status = 1;
            }

            $task->completed = serialize($completed);
            $task->save();
            return static::response_data();
        }
    }

    //客户端升级检查接口
    public function get_version_check()
    {
        $validation = static::validation(array(
            'user_id'         => 'required',
            'local_version'   => 'required'
        ));
        if($validation !== true) return $validation;

        $version = Version::where('version_code','>',Input::get('local_version'))
            ->order_by('version_code','desc')
            ->first(array('version','content','is_force','download_url'));

        if($version == null)
        {
            $data['status']   = 'newest';
        }
        else
        {
            $data['status']   = 'old';
            $data['version'] = $version->version;
            $data['content'] = $version->content;
            $data['download_url'] = Helper::add_site_uri($version->download_url);
            $data['is_force'] = $version->is_force == 1 ? true : false;
        }
        return static::response_data($data);
    }

    //意见反馈接口
    public function post_suggest_submit()
    {
        $validation = static::validation(array(
            'user_id' => 'required',
            'content' => 'required|max:256|min:2'
        ));
        if($validation !== true) return $validation;

        $suggest = new Suggest();
        $suggest->user_id = Input::get('user_id');
        $suggest->content = Input::get('content');
        $suggest->save();
        return static::response_data();
    }

    //客户端错误提交接口
    public function post_exception_submit()
    {
        $exception = new ExceptionLog();
        $exception->content = Input::get('content');
        $exception->save();
        return static::response_data();
    }

    //私有参数验证
    private static function validation($rules)
    {
        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            return static::response_error($validation->errors->first());
        }
        else
        {
            return true;
        }
    }

    //私有构造数据输出
    private static function response_data($data = null, $status_code = 200, $type = 'array')
    {
        //判断Eloquent对象
        if($type == 'eloquent')
        {
            $data = array_map(function($data)
            {
                return $data->to_array();
            }, $data);
        }
        $data_build['status_code'] = $status_code;
        if($data != null || is_array($data))
            $data_build['data'] = $data;
        return \Laravel\Response::json($data_build,200);
    }

    //私有构造报错
    private static function response_error($error_msg, $status_code = 400)
    {
        $data_build['status_code'] = $status_code;
        $data_build['error_msg']   = $error_msg;
        return \Laravel\Response::json($data_build,200);
    }

}