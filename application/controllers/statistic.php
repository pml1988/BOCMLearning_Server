<?php
/**
 * Author: RaymondChou
 * Date: 13-1-7
 * File: statistic.php
 * Email: zhouyt.kai7@gmail.com
 */
class Statistic_Controller extends Base_Controller {

    public $restful = true;

    public function get_admin_log_list()
    {
        $view = View::make('statistic/admin_log_list');
        $page = \Laravel\Input::get('page',1);
        $per_page = 20;

        $orm_obj = AdminLog::with('user');

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
            $orm_obj = $orm_obj->where('user_id','=',Auth::user()->id);
        }

        $total = $orm_obj->count();

        $view->list = $orm_obj
            ->order_by('created_at','desc')
            ->take($per_page)
            ->skip(($page-1)*$per_page)
            ->get();

        $view->web_title = '管理日志';
        $view->page_link = Paginator::make($view->list, $total, $per_page)
            ->appends(array(
                'search_start' => \Laravel\Input::get('search_start'),
                'search_end'   => \Laravel\Input::get('search_end')
            ));

        return $view;
    }

    public function get_traffic()
    {
        $view = View::make('statistic/traffic');

        $chart['date'] = '';
        $chart['count_request'] = '';
        $chart['count_user'] = '';

        if(Input::has('date'))
        {
            $y = explode('-',Input::get('date'))[0];
            $m = explode('-',Input::get('date'))[1];
        }
        else
        {
            $y = date('Y');
            $m = date('m');
        }

        $d = Input::has('date') && $m != date('m') ? 31 : date('d');

        for($i=1;$i<=$d;$i++)
        {
            $chart['date'] .= $i.',';
            $chart['count_request'] .= ApiLog::where('d','=',$i)
                ->where('m','=',$m)
                ->where('y','=',$y)
                ->count().',';

            $count_user = ApiLog::
                distinct()
                ->select(array('user_id'))
                ->where('d','=',$i)
                ->where('m','=',$m)
                ->where('y','=',$y)
                ->where_not_null('user_id')
                ->get();
            $chart['count_user'] .= count($count_user).',';
        }

        $view->chart = $chart;
        $view->total = ApiLog::count();
        $view->web_title = '流量统计';
        return $view;
    }

    public function get_product()
    {
        $view = View::make('statistic.product');
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

        $orm_obj = $orm_obj->order_by('hot', 'desc')
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

        $view->web_title = '产品统计';
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