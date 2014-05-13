<?php

class Product_Controller extends Base_Controller {

    public $restful = true;

	public function get_type_list()
	{
        if(!Input::has('top_id'))
        {
            $view = View::make('product.type_list');
            $page = \Laravel\Input::get('page',1);
            $per_page = 20;
            $total = ProductType::where('level','=',1)->count();

            $view->list = ProductType::order_by('sort', 'asc')
                ->where('level','=',1)
                ->take($per_page)
                ->skip(($page-1)*$per_page)
                ->get();

            $view->web_title = '产品分类管理';
        }
        else
        {
            $view = View::make('product.type_list');
            $page = \Laravel\Input::get('page',1);
            $per_page = 20;
            $total = ProductType::where('top_id','=',Input::get('top_id'));
            if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
            {
                $total = $total->where_in('id',Util::role_describe('product'));
            }
            $total = $total ->count();


            $list = ProductType::where('top_id','=',Input::get('top_id'));

            if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
            {
                $list = $list->where_in('id',Util::role_describe('product'));
            }

            $view->list = $list->order_by('sort', 'asc')
                ->take($per_page)
                ->skip(($page-1)*$per_page)
                ->get();

            $top_name = ProductType::where('id','=',Input::get('top_id'))
                ->first(array('name'));

            $view->web_title = $top_name->name.' - 子分类';
        }

        $view->page_link = Paginator::make($view->list, $total, $per_page);
        return $view;
	}

    public function get_type_add()
    {
        return View::make('product.type_add')
            ->with(array('web_title' => '添加产品分类'));
    }

    public function post_type_add()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
            'sort'  => 'required|integer',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = new ProductType();
        $type->name = Input::get('name');
        $type->sort = Input::get('sort');
        $type->top_id = Input::get('top_id');
        $type->level = Input::has('top_id') ? 2 : 1;
        $type->status = Input::has('status') ? 1 : 0;
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加产品分类');

        return Redirect::to(Input::has('top_id')
            ? 'product/type_list?top_id='.Input::get('top_id')
            : 'product/type_list');
    }

    public function get_type_edit()
    {
        $top_types = ProductType::where('level','=',1)->get(array('id','name'));
        $type = ProductType::find(Input::get('id'));

        if($type == null)
            return \Laravel\Response::error(404);

        return View::make('product.type_edit')
            ->with('web_title','编辑产品分类')
            ->with('type',$type)
            ->with('top_types',$top_types);
    }

    public function post_type_edit()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
            'sort'  => 'required|integer',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = ProductType::find(Input::get('id'));
        $type->name = Input::get('name');
        $type->sort = Input::get('sort');
        $type->top_id = Input::get('top_id');
        $type->level = Input::has('top_id') ? 2 : 1;
        $type->status = Input::has('status') ? 1 : 0;
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品分类');

        return Redirect::to(Input::has('top_id')
            ? 'product/type_list?top_id='.Input::get('top_id')
            : 'product/type_list');
    }

    public function get_type_del()
    {
        $type = ProductType::find(\Laravel\Input::get('id'));
        $type->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品分类');

        return Redirect::back();
    }

    public function get_product_list()
    {
        $view = View::make('product.product_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $orm_obj = Product::with('product_type');

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $orm_obj = $orm_obj->where_in('product_type_id',Util::role_describe('product'));
        }

        if(Input::has('search_name'))
        {
            $orm_obj = $orm_obj->where('product_name','like','%'.Input::get('search_name').'%');
        }
        if(Input::has('search_type'))
        {
            $orm_obj = $orm_obj->where('product_type_id','=',Input::get('search_type'));
        }
        if(Input::has('search_start'))
        {
            $orm_obj = $orm_obj->where('created_at','>=',Input::get('search_start'));
        }
        if(Input::has('search_end'))
        {
            $orm_obj = $orm_obj->where('created_at','<=',date('Y-m-d',strtotime(Input::get('search_end'))+3600*24));
        }

        $total = $orm_obj->count();

        $orm_obj = $orm_obj->order_by('sort', 'asc')
                           ->take($per_page)
                           ->skip(($page-1)*$per_page);

        $view->list = $orm_obj->get();

        $types = ProductType::where('level','=',2);

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $types = $types->where_in('id',Util::role_describe('product'));
        }

        $view->types = $types
                ->where('status','=',1)
                ->order_by('top_id','desc')
                ->get(array('id','name','top_id'));

        $view->web_title = '产品管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'search_name'  => \Laravel\Input::get('search_name'),
                'search_type'  => \Laravel\Input::get('search_type'),
                'search_start' => \Laravel\Input::get('search_start'),
                'search_end'   => \Laravel\Input::get('search_end')
            ));

        return $view;
    }

    public function get_product_add()
    {
        $types = ProductType::where('level','=',2);

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $types = $types->where_in('id',Util::role_describe('product'));
        }

        $types = $types
            ->where('status','=',1)
            ->order_by('top_id','desc')
            ->get(array('id','name','top_id'));

        $tags = ProductTag::all();

        return View::make('product.product_add')
            ->with(array('web_title' => '添加产品'))
            ->with('types',$types)
            ->with('tags',$tags);
    }

    public function post_product_add()
    {
        $rules = array(
            'product_name'  => 'required|max:32|min:2',
            'sort'          => 'required|integer',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $product['product_name']    = Input::get('product_name');
        $product['sort']            = Input::get('sort');
        $product['product_type_id'] = Input::get('product_type');
        $product['info']            = Input::get('info');
        $product['video_url']       = Input::get('video_url');
        $product['status']          = Input::has('status') ? 1 : 0;
        $product['is_suggest']      = Input::has('is_suggest') ? 1 : 0;

        $product_id = DB::table('products')->insert_get_id($product);

        $orm_obj = Product::find($product_id);

        if(Input::get('product_attribute') != null)
        {
            foreach(Input::get('product_attribute') as $attribute_data)
            {
                $attribute_data = explode('||',$attribute_data);
                $product_attribute = array(
                    'value'   => $attribute_data[1],
                    'sort'    => $attribute_data[2],
                    'display' => $attribute_data[3]
                );
                $orm_obj->product_attribute()->attach($attribute_data[0], $product_attribute);
            }
        }

        if(Input::get('image') != null)
        {
            foreach(Input::get('image') as $image_url)
            {
                $image = New Image();
                $image->url        = $image_url;
                $image->product_id = $product_id;
                $image->save();
            }
        }

        if(Input::get('product_tags') != null)
        {
            foreach(Input::get('product_tags') as $tag)
            {
                $orm_obj->product_tag()->attach($tag);
            }
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加产品');

        return Redirect::to('product/product_list');
    }

    public function get_product_edit()
    {
        $product = Product::find(Input::get('id'));

        $types = ProductType::where('level','=',2);

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $types = $types->where_in('id',Util::role_describe('product'));
        }

        $types = $types
            ->where('status','=',1)
            ->order_by('top_id','desc')
            ->get(array('id','name','top_id'));

        $tags = ProductTag::all();

        if($product == null)
            return \Laravel\Response::error(404);

        return View::make('product.product_edit')
            ->with('web_title','编辑产品')
            ->with('product',$product)
            ->with('tags',$tags)
            ->with('types',$types);
    }

    public function post_product_edit()
    {
        $rules = array(
            'product_name'  => 'required|max:32|min:2',
            'sort'          => 'required|integer',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }
        $orm_obj = Product::find(Input::get('id'));

        $orm_obj->product_name    = Input::get('product_name');
        $orm_obj->sort            = Input::get('sort');
        $orm_obj->product_type_id = Input::get('product_type');
        $orm_obj->info            = Input::get('info');
        $orm_obj->video_url       = Input::get('video_url');
        $orm_obj->status          = Input::has('status') ? 1 : 0;
        $orm_obj->is_suggest      = Input::has('is_suggest') ? 1 : 0;

        $orm_obj->save();

        $orm_obj->product_attribute()->delete();
        if(Input::get('product_attribute') != null)
        {
            foreach(Input::get('product_attribute') as $attribute_data)
            {
                $attribute_data = explode('||',$attribute_data);
                $product_attribute = array(
                    'value'   => $attribute_data[1],
                    'sort'    => $attribute_data[2],
                    'display' => $attribute_data[3]
                );
                $orm_obj->product_attribute()->attach($attribute_data[0], $product_attribute);
            }
        }

        $orm_obj->image()->delete();
        if(Input::get('image') != null)
        {
            foreach(Input::get('image') as $image_url)
            {
                $image = New Image();
                $image->url        = $image_url;
                $image->product_id = Input::get('id');
                $image->save();
            }
        }

        $orm_obj->product_tag()->delete();
        if(Input::get('product_tags') != null)
        {
            foreach(Input::get('product_tags') as $tag)
            {
                $orm_obj->product_tag()->attach($tag);
            }
        }

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品');

        return Redirect::to(URL::to(base64_decode(Input::get('ref'))));
    }

    public function get_product_del()
    {
        $product = Product::find(\Laravel\Input::get('id'));
        $product->delete();
        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品');

        return Redirect::back();
    }

    public function get_product_suggest()
    {
        $product = Product::find(\Laravel\Input::get('id'));
        if($product->is_suggest == 1)
            $product->is_suggest = 0;
        else
            $product->is_suggest = 1;
        $product->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'设置产品推荐');

        return Redirect::back();
    }


    public function get_attribute_list()
    {
        $view = View::make('product.attribute_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $total = ProductAttribute::count();

        $view->list = ProductAttribute::
            take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '产品详情字段管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page);

        return $view;
    }

    public function get_attribute_add()
    {
        return View::make('product.attribute_add')
            ->with(array('web_title' => '添加产品详情字段'));
    }

    public function post_attribute_add()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = new ProductAttribute();
        $type->name = Input::get('name');
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加产品详情字段');

        return Redirect::to('product/attribute_list');
    }

    public function get_attribute_edit()
    {
        $attribute = ProductAttribute::find(Input::get('id'));

        if($attribute == null)
            return \Laravel\Response::error(404);

        return View::make('product.attribute_edit')
            ->with('web_title','编辑产品详情字段')
            ->with('attribute',$attribute);
    }

    public function post_attribute_edit()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $attribute = ProductAttribute::find(Input::get('id'));
        $attribute->name = Input::get('name');
        $attribute->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品详情字段');

        return Redirect::to('product/attribute_list');
    }

    public function get_attribute_del()
    {
        $type = ProductAttribute::find(\Laravel\Input::get('id'));
        $type->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品详情字段');

        return Redirect::back();
    }

    public function get_tag_list()
    {
        $view = View::make('product.tag_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $total = ProductTag::count();

        $view->list = ProductTag::
            take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '产品标签管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page);

        return $view;
    }

    public function get_tag_add()
    {
        return View::make('product.tag_add')
            ->with(array('web_title' => '添加产品标签'));
    }

    public function post_tag_add()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2',
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $type = new ProductTag();
        $type->name = Input::get('name');
        $type->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'添加产品标签');

        return Redirect::to('product/tag_list');
    }

    public function get_tag_edit()
    {
        $tag = ProductTag::find(Input::get('id'));

        if($tag == null)
            return \Laravel\Response::error(404);

        return View::make('product.tag_edit')
            ->with('web_title','编辑产品标签')
            ->with('tag',$tag);
    }

    public function post_tag_edit()
    {
        $rules = array(
            'name'  => 'required|max:16|min:2'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $tag = ProductTag::find(Input::get('id'));
        $tag->name = Input::get('name');
        $tag->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品标签');

        return Redirect::to('product/tag_list');
    }

    public function get_tag_del()
    {
        $type = ProductTag::find(\Laravel\Input::get('id'));
        $type->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品标签');

        return Redirect::back();
    }

    public function get_product_comment_list()
    {
        $view = View::make('product.product_comment_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $total = ProductComment::where('product_id','=',Input::get('id'))
            ->count();

        $view->list = ProductComment::
            with('user')
            ->where('product_id','=',Input::get('id'))
            ->order_by('is_top','desc')
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '产品评论管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'id'   => Input::get('id'),
                'ref'  => Input::get('ref')
            ));

        return $view;
    }

    public function get_product_comment_top()
    {
        $comment = ProductComment::find(\Laravel\Input::get('id'));
        if($comment->is_top == 1)
            $comment->is_top = 0;
        else
            $comment->is_top = 1;
        $comment->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'设置产品评论置顶');

        return Redirect::back();
    }

    public function get_product_comment_del()
    {
        $comment = ProductComment::find(\Laravel\Input::get('id'));
        $comment->delete();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'删除产品评论');

        return Redirect::back();
    }

    public function get_product_comment_edit()
    {
        $comment = ProductComment::find(Input::get('id'));

        if($comment == null)
            return \Laravel\Response::error(404);

        return View::make('product.product_comment_edit')
            ->with('web_title','编辑评论')
            ->with('comment',$comment);
    }

    public function post_product_comment_edit()
    {
        $rules = array(
            'content'  => 'required|max:256|min:2',
            'score'    => 'required|integer'
        );

        $validation = Validator::make(Input::get(), $rules);
        if ($validation->fails())
        {
            Messages::add('error',$validation->errors->all());
            return  Redirect::back()
                ->with_input();
        }

        $comment = ProductComment::find(Input::get('id'));
        $comment->content = Input::get('content');
        $comment->score   = Input::get('score');
        $comment->is_top  = Input::has('is_top') ? 1 : 0;
        $comment->save();

        Messages::add('success','操作成功!');
        Util::save_admin_log(__FUNCTION__,'修改产品评论');

        return Redirect::to(URL::to(base64_decode(Input::get('ref'))));
    }

    public function get_product_list_with_comment()
    {
        $view = View::make('product.product_list_with_comment');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;
        $orm_obj = Product::with('product_type');

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $orm_obj = $orm_obj->where_in('product_type_id',Util::role_describe('product'));
        }

        if(Input::has('search_name'))
        {
            $orm_obj = $orm_obj->where('product_name','like','%'.Input::get('search_name').'%');
        }
        if(Input::has('search_type'))
        {
            $orm_obj = $orm_obj->where('product_type_id','=',Input::get('search_type'));
        }
        if(Input::has('search_start'))
        {
            $orm_obj = $orm_obj->where('created_at','>=',Input::get('search_start'));
        }
        if(Input::has('search_end'))
        {
            $orm_obj = $orm_obj->where('created_at','<=',date('Y-m-d',strtotime(Input::get('search_end'))+3600*24));
        }

        $total = $orm_obj->count();

        $orm_obj = $orm_obj->order_by('updated_at', 'desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page);

        $view->list = $orm_obj->get();

        $types = ProductType::where('level','=',2);

        if(strpos(Auth::user()->roles,'admin') == false AND strpos(Auth::user()->roles,'root') == false)
        {
            $types = $types->where_in('id',Util::role_describe('product'));
        }

        $view->types = $types
            ->where('status','=',1)
            ->order_by('top_id','desc')
            ->get(array('id','name','top_id'));

        $view->web_title = '产品评论管理';
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'search_name'  => \Laravel\Input::get('search_name'),
                'search_type'  => \Laravel\Input::get('search_type'),
                'search_start' => \Laravel\Input::get('search_start'),
                'search_end'   => \Laravel\Input::get('search_end')
            ));

        return $view;
    }

}