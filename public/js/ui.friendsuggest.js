if(typeof giant === 'undefined')
    var giant = window.giant = {};
if(typeof giant.ui === 'undefined')
    giant.ui = {};
(function($) {

     giant.ui.friendsuggest = function(options) {
        this.opts = $.extend({}, giant.ui.friendsuggest.defaults, options);
        this.currentPage = 1;
        this.totalPage =1;
        this.resultArr = [];
        this.isAllFriendShow = false;   //是否已经显示所有好友
        this.isDropDownListShow = false; //下拉选项是否已显示
        this.activeIndex = 0;
        this.typeId = -1;
        this._init();
    };
    giant.ui.friendsuggest.prototype = {
        _init:function() {
            this._formartSelect();
            this._getRecordCound();
            this._clickBind();
            this._focusBind();
            this._blurBind();
            this._keyUpBind();
            this._selectChangeBind();
        },
        _formartSelect:function(){
            var $this= this;
             $.ajax({
                url:$this.opts.ajaxGetFriendTypeUrl,
                success:function(msg) {
                    var myData = eval(msg);
                    var arr=[];
                    $.each(myData,function(i,n){
                        arr.push("<option value='"+n.id+"'>"+n.name+"</option>");
                    });
                    $($this.opts.selectFriendType).append(arr.join(''));
                }
            });
        },
        _selectChangeBind:function(){
            var $this = this;
             $(this.opts.selectFriendType).bind("change",function(){
                $this.typeId = $(this).val();
                $this.currentPage = 1;
                $this._getRecordCound($this._formartAllFriend);
             });
        },
        _clickBind:function() {
            var $this = this;
            //右侧查看所有小图标
            $($this.opts.btnAll).bind("click", function() {
                if (!$this.isAllFriendShow) {
                     $this._formartAllFriend();
                } else {
                    $this.setAllFriendHide();
                }

            });
            //查看所有中的关闭按钮
            $($this.opts.btnCloseAllFriend).bind("click", function() {
                $this.setAllFriendHide();
            });
            //点击添加好友
            $($this.opts.allFriendListContainer).find("a").live("click", function() {
                $this.addUser($(this).attr("name"), $(this).text());
                alert('add');
            });
            //点击删除好友
            $($this.opts.resultContainer).find("span").live("click", function() {
                $this.deleteUser($(this).parents("a"));
            });
            //下一页
            $($this.opts.btnNextPage).bind("click", function() {
                if ($this.currentPage < $this.totalPage) {
                    $this.currentPage++;
                    $this._getData($this._getPar(), $($this.opts.allFriendListContainer), null, "all");
                    if ($this.currentPage == $this.totalPage) {
                        $($this.opts.btnNextPage).addClass("disable");
                    }
                    if ($this.currentPage > 1) {
                        $($this.opts.btnPrevPage).removeClass("disable");
                    }
                }
            });
            //上一页
            $($this.opts.btnPrevPage).bind("click", function() {
                if ($this.currentPage > 1) {
                    $this.currentPage--;
                    $this._getData($this._getPar(), $($this.opts.allFriendListContainer), null, "all");
                    if ($this.currentPage < $this.totalPage) {
                        $($this.opts.btnNextPage).removeClass("disable");
                    }
                    if ($this.currentPage == 1) {
                        $($this.opts.btnPrevPage).addClass("disable");
                    }
                }
            });
        },
        _focusBind:function() {
            var $this = this;
            $($this.opts.input).bind("focus", function() {
                $(this).val("");
                $this.setAllFriendHide();
                $($this.opts.inputContainer).css("border-color", "#7f9db9");
            });
        },
        _blurBind:function() {
            var $this = this;
            $($this.opts.input).bind("blur", function() {
                $($this.opts.inputContainer).css("border-color", "#999");
                setTimeout(function() {
                    $this.setDropDownListHide();
                }, 200);
            });
        },
        _keyUpBind:function() {
            var $this = this;
            $(this.opts.input).bind("keyup", function(event) {
                if (event.keyCode != "40" && event.keyCode != "38" && event.keyCode != "39" && event.keyCode != "37" && event.keyCode != "13") {
                    if ($($this.opts.input).val().length > 0) {
                        $this.setDropDownListShow();
                        $this._getData("name=" + $(this).val(), $($this.opts.dropDownListContainer), function() {
                            $($this.opts.dropDownListContainer).find("a").eq(0).addClass("active");
                            $($this.opts.dropDownListContainer).find("a").each(function(index) {
                                $(this).bind("mouseover", function() {
                                    $this.activeIndex = index;
                                    $($this.opts.dropDownListContainer).find("a").removeClass("active");
                                    $($this.opts.dropDownListContainer).find("a").eq($this.activeIndex).addClass("active");
                                });
                                $(this).bind("click", function() {
                                    var $obj = $($this.opts.dropDownListContainer).find("a.active");
                                    $this.addUser($obj.attr("name"), $obj.text());
                                    $this.setDropDownListHide();
                                    $($this.opts.input).focus();
                                });
                            });
                        },"normal");
                    }
                    else {       //输入为空
                        $($this.opts.dropDownListContainer).html($this.opts.inputDefaultTip);
                    }
                }
                if ($this.isDropDownListShow) {
                    var totalCount = $($this.opts.dropDownListContainer).find("a").size();
                    if (totalCount > 0) {
                        //down 键
                        if (event.keyCode == "40") {
                            if ($this.activeIndex < totalCount - 1) $this.activeIndex++;
                            else  $this.activeIndex = 0;
                        }
                        //up 键
                        else if (event.keyCode == "38") {
                            if ($this.activeIndex > 0)  $this.activeIndex--;
                            else  $this.activeIndex = totalCount - 1;
                        }
                        //回车键  加入
                        else if (event.keyCode == "13") {
                                var $obj = $($this.opts.dropDownListContainer).find("a.active");
                                $this.addUser($obj.attr("name"), $obj.text());
                                $this.setDropDownListHide();
                            }
                        $($this.opts.dropDownListContainer).find("a").removeClass("active");
                        $($this.opts.dropDownListContainer).find("a").eq($this.activeIndex).addClass("active");
                    }
                }
            });
        },
        _formartAllFriend:function() {
            var $this = this;
            $this.setAllFriendShow();
            $this._getData($this._getPar(), $($this.opts.allFriendListContainer), null, "all");
            //上一页下一页判断
            if ($this.currentPage == 1) {
                $($this.opts.btnPrevPage).addClass("disable");
            }
            if ($this.currentPage == $this.totalPage || $this.totalPage == 0) {
                $($this.opts.btnNextPage).addClass("disable");
            }
        },
        _getData:function(data, $container, callBack, type) {         //全部friend 列表
            var callBack = callBack || null;
            var type=type||"normal";
            var ajaxUrl="";
            if(type=="normal"){
                ajaxUrl = this.opts.ajaxUrl;
            }
            else{
                ajaxUrl=this.opts.ajaxLoadAllUrl;
            }
            var $this = this;
            if ($this.xmlHttpObj) {
                $this.xmlHttpObj.abort();
                $this.xmlHttpObj = null;
            }
            this.xmlHttpObj = $.ajax({
                url:ajaxUrl,
                data:data,
                before:function() {
                    if ($.isFunction($this.opts.ajaxBefore)) {
                        $this.opts.ajaxBefore.call($this);
                    }
                },
                success:function(msg) {
                    var myData = eval(msg);
                    if (myData.length > 0) {
                        var arr = [];
                        $.each(myData, function(i, n) {
                            arr.push("<a href='javascript:void(0)' title='" + n.name + "' name='" + n.id + "'>" + n.name + "</a>");
                        });
                        $container.html(arr.join(''));
                    } else {
                        $container.html($this.opts.noDataTip);
                    }
                    if ($.isFunction(callBack)) {
                        callBack();
                    }
                },
                error:function() {
                    if ($.isFunction($this.opts.ajaxError)) {
                        $this.opts.ajaxError.call($this);
                    }
                }
            });
        },
        _getRecordCound:function(callBack){
            var $this =this;
            var data ="";
            if(this.typeId!=-1){
               data="typeId="+this.typeId; 
            }
             $.ajax({
                url:$this.opts.ajaxGetCountUrl,
                data:data,
                success:function(msg) {
                    var myData = eval(msg);
                    if(!window.isNaN(myData)){
                        $this.totalPage = Math.ceil(myData/12.0);
                    }
                    if($.isFunction(callBack)){
                        callBack.call($this);
                    }
                }
            });
        },
        _getPar:function() {
            var data = "pageSize=12&p=" + this.currentPage;
            if (this.typeId != -1) {
                data += "&type=" + this.typeId;
            }
            return data;
        },
        /**
         * 添加用户
         * @param{Number} id 好友的id
         * @name {String} name 好友的名字
         * @image{String} name 图片路径
         * */
        addUser:function(id, name) {
            var $this = this;
            //如果当前用户不存在
            if ($this.opts.selectType == "multiple") {
                if ($.inArray(id, $this.resultArr) == -1) {
                    $this.resultArr.push(id);
                    $($this.opts.resultContainer).append("<a href='javascript:void(0)' name='" + id + "'> " + name + "<span title='移除该好友'>移除该好友</span></a>")
                    $($this.opts.frinedNumberContainer).text(30 - $this.resultArr.length);
                } else {
                    var i = 0;
                    var $obj = $($this.opts.resultContainer).find("[name='" + id + "']");
                    $obj.css("background-color", "#fff");
                    //变色
                    var interval = setInterval(function() {
                        //IE和FF颜色输出不一样
                        if ($obj.css("background-color") == "#ffffff" || $obj.css("background-color") == "rgb(255, 255, 255)") {
                            $obj.css("background-color", "#6699cc");
                            $obj.css("color", "#fff");
                        } else {
                            $obj.css("background-color", "#ffffff");
                            $obj.css("color", "#666666");
                        }
                        i++;
                        if (i == 4) {
                            $obj.attr("style", "");
                            clearInterval(interval);
                        }
                    }, 300);
                }
            } else {
                if ($.isFunction($this.opts.selectCallBack)) {
                    $this.opts.selectCallBack.call($this, id, name);
                }
            }
        },
        deleteUser:function($obj) {
            var $this = this;
            $this.resultArr = [];
            $obj.siblings("a").each(function() {
                $this.resultArr.push($(this).attr("name"));
            });
            $obj.remove();
            $($this.opts.frinedNumberContainer).text(30 - $this.resultArr.length);
        },
        /**
         * 显示好友提示下拉层
         * */
        setDropDownListShow:function() {
            this.isDropDownListShow = true;
            this.setAllFriendHide();
            $(this.opts.dropDownListContainer).show();
        },
         /**
         * 隐藏好友提示下拉层
         * */
        setDropDownListHide:function() {
            this.isDropDownListShow = false;
            this.activeIndex = 0;
            $(this.opts.dropDownListContainer).hide();
            $(this.opts.input).val("");
            $(this.opts.dropDownListContainer).html("");
        },
         /**
         * 显示所有好友选择框
         * */
        setAllFriendShow:function() {
            this.isAllFriendShow = true;
            this.setDropDownListHide();
            $(this.opts.allFriendContainer).show();
            $(this.opts.btnAll).addClass("active");
        },
         /**
         * 隐藏所有好友选择框
         * */
        setAllFriendHide:function() {
            this.isAllFriendShow = false;
            $(this.opts.allFriendContainer).hide();
            $(this.opts.btnAll).removeClass("active");
        },
        /**
         * 获取选中的好友结果集
         * @return {Array} 返回存放选中的好友id的数组
         * */
        getResult:function() {
            return this.resultArr;
        }
    }
    /**
     * 默认参数
     * <pre>
     * totalSelectNum 多选模式下，最多选取人数，默认为30
     * selectType 选择模式，默认为多选"multiple",若为单选，则用single
     * selectCallBack 单选模式下，选中之后的回调函数。 
     * </pre>
     * */
    giant.ui.friendsuggest.defaults = {
        btnAll:"#ui-fs .ui-fs-icon",
        btnCloseAllFriend:"#ui-fs .ui-fs-all .close",
        btnNextPage:"#ui-fs .ui-fs-all .next",
        btnPrevPage:"#ui-fs .ui-fs-all .prev",
        selectFriendType:"#ui-fs-friendtype",
        allFriendContainer:"#ui-fs .ui-fs-all" ,
        allFriendListContainer:"#ui-fs .ui-fs-all .ui-fs-allinner div.list",
        frinedNumberContainer:"#ui-fs .ui-fs-allinner .page b",
        resultContainer:"#ui-fs .ui-fs-result",
        input:"#ui-fs .ui-fs-input input",
        inputContainer:"#ui-fs .ui-fs-input",
        dropDownListContainer:"#ui-fs .ui-fs-list",
        inputDefaultTip:"输入姓名或工号",
        noDataTip:"不存在该用户",
        ajaxUrl:"http://"+location.hostname+"/ajax/find_user/",
        ajaxLoadAllUrl:"http://"+location.hostname+"/ajax/find_user/",
//        ajaxGetCountUrl:"http://"+location.hostname+"/ajax/partner_count/",
//        ajaxGetFriendTypeUrl:"http://"+location.hostname+"/ajax/area_type/",
        totalSelectNum:30,
        ajaxBefore:null,
        ajaxError:null,
        selectType:"multiple",
        selectCallBack:null
    };
})(jQuery);
