<nav class="main-navigation" role="navigation">
    <ul>

        <li {{URI::is('dashboard') ? 'class="current"' : ''}} >
            <a href="{{URL::to('dashboard')}}" class="no-submenu"><span class="awe-home"></span>控制台</a>
        </li>

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('product') === true)
        <li {{URI::is('product/type_*') ? 'class="current"' : ''}} >
            <a href="{{URL::to('product/type_list')}}" class="no-submenu"><span class="awe-tags"></span>产品分类管理</a>
        </li>

        <li {{URI::is('product/attribute_*') ? 'class="current"' : ''}} >
        <a href="{{URL::to('product/attribute_list')}}" class="no-submenu"><span class="awe-check"></span>产品详情字段管理</a>
        </li>

        <li {{URI::is('product/tag_*') ? 'class="current"' : ''}} >
        <a href="{{URL::to('product/tag_list')}}" class="no-submenu"><span class="awe-cloud"></span>产品标签管理</a>
        </li>

        <li {{URI::is('product/product_*') && !URI::is('product/*comment*') ? 'class="current"' : ''}} >
            <a href="{{URL::to('product/product_list')}}" class="no-submenu"><span class="awe-tasks"></span>产品管理 <span class="badge">{{Product::where('status','=',1)->count()}}</span></a>
        </li>

        <li {{URI::is('product/*comment*') ? 'class="current"' : ''}} >
        <a href="{{URL::to('product/product_list_with_comment')}}" class="no-submenu"><span class="awe-comment"></span>产品评论管理 </a>
        </li>
        @endif

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('question') === true)
        <li {{URI::is('question/type_*') ? 'class="current"' : ''}} >
            <a href="{{URL::to('question/type_list')}}" class="no-submenu"><span class="awe-tags"></span>问答分类管理</a>
        </li>

        <li {{( URI::is('question/question_*') OR URI::is('question/answer_*') ) ? 'class="current"' : ''}} >
            <a href="{{URL::to('question/question_list')}}" class="no-submenu"><span class="awe-comments-alt"></span>问答管理
                <span class="badge">{{Question::where('status','=',1)->count()}}</span></a>
        </li>
        @endif

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('learn') === true)
        <li {{URI::is('learn/*') ? 'class="current"' : ''}} >
        <a href="#"><span class="awe-book"></span>学习管理</a>
        <ul>
            <li>
                <a {{URI::is('learn/group*') ? 'class="current"' : ''}} href="{{URL::to('learn/group_list')}}">学习小组管理</a>
            </li>
            <li>
                <a {{URI::is('learn/task*') ? 'class="current"' : ''}} href="{{URL::to('learn/task_list')}}">学习任务管理</a>
            </li>
        </ul>
        </li>
        @endif

        <li {{URI::is('statistic/*') ? 'class="current"' : ''}} >
            <a href="#"><span class="awe-signal"></span>统计分析</a>
            <ul>
                <li>
                    <a {{URI::is('statistic/traffic') ? 'class="current"' : ''}} href="{{URL::to('statistic/traffic')}}">访问/流量统计</a>
                </li>
                <li><a {{URI::is('statistic/product') ? 'class="current"' : ''}} href="{{URL::to('statistic/product')}}">产品统计</a></li>
                <li>
                    <a {{URI::is('statistic/admin_log_*') ? 'class="current"' : ''}} href="{{URL::to('statistic/admin_log_list')}}">管理日志</a>
                </li>
            </ul>
        </li>

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('user') === true)
        <li {{URI::is('user/*') ? 'class="current"' : ''}} >
            <a href="#"><span class="awe-user"></span>用户管理</a>
            <ul>
                <li>
                    <a {{URI::is('user/tree') ? 'class="current"' : ''}} href="{{URL::to('user/tree')}}">组织构架</a>
                </li>
                <li>
                    <a {{URI::is('user/user_*') ? 'class="current"' : ''}} href="{{URL::to('user/user_list')}}">用户管理</a>
                </li>
                <li>
                    <a {{URI::is('user/level') ? 'class="current"' : ''}} href="{{URL::to('user/level')}}">积分管理</a>
                </li>
            </ul>
        </li>
        @endif

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('role') === true OR (Bouncer::investigate(Auth::user())->allow_or_block_on('role/learn_role_list') === true AND Auth::user()->line <= 1030))
        <li {{URI::is('role/*') ? 'class="current"' : ''}} >
            <a href="#"><span class="awe-group"></span>角色管理</a>
            <ul>
                @if(Bouncer::investigate(Auth::user())->allow_or_block_on('role') === true)
                <li>
                    <a {{URI::is('role/admin_role*') ? 'class="current"' : ''}} href="{{URL::to('role/admin_role_list')}}">系统管理员</a>
                </li>
                <li>
                    <a {{URI::is('role/product_role*') ? 'class="current"' : ''}} href="{{URL::to('role/product_role_list')}}">产品管理员</a>
                </li>
                <li>
                    <a {{URI::is('role/question_role*') ? 'class="current"' : ''}} href="{{URL::to('role/question_role_list')}}">问答管理员</a>
                </li>
                @endif
                <li>
                    <a {{URI::is('role/learn_role*') ? 'class="current"' : ''}} href="{{URL::to('role/learn_role_list')}}">学习管理员</a>
                </li>
            </ul>
        </li>
        @endif

        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('system') === true)
        <li {{URI::is('system/*') ? 'class="current"' : ''}} >
            <a href="#"><span class="awe-cogs"></span>系统管理</a>
            <ul>
                <li>
                    <a {{URI::is('system/suggest_list') ? 'class="current"' : ''}} href="{{URL::to('system/suggest_list')}}">意见反馈</a>
                </li>
                <li>
                    <a {{URI::is('system/setting') ? 'class="current"' : ''}} href="{{URL::to('system/setting')}}">系统设置</a>
                </li>
                <li>
                    <a {{URI::is('system/hr_data') ? 'class="current"' : ''}} href="{{URL::to('system/hr_data')}}">HR数据导入</a>
                </li>
                <li>
                    <a {{URI::is('system/version_*') ? 'class="current"' : ''}} href="{{URL::to('system/version_list')}}">手机客户端版本管理</a>
                </li>
            </ul>
        </li>
        @endif

    </ul>
</nav>