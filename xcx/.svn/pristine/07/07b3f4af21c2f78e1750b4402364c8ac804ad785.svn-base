<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/public/css/admin/x-admin.css?t=<?php echo JsVer; ?>" media="all">
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header header-demo">
        <div class="layui-main">
            <a class="logo" href="">

            </a>
            <ul class="layui-nav" lay-filter="">
                <li class="layui-nav-item"><img src="<?php echo $_SESSION['img']; ?>" class="layui-circle"
                                                style="border: 2px solid #A9B7B7;" width="35px" height="35px"></li>
                <li class="layui-nav-item">
                    <a href="javascript:;"><?php echo $_SESSION['username']; ?></a>
                    <dl class="layui-nav-child"> <!-- 二级菜单 -->
                        <dd><a href="">我的桌面</a></dd>
                        <dd><a href="/xiamenyyhoutai/login/logout">退出</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="" title="消息">
                        <i class="layui-icon" style="top: 1px;">&#xe63a;</i>
                    </a>
                </li>
                <!--<li class="layui-nav-item x-index"><a href="/" target="_blank">前台首页</a></li>-->
            </ul>
        </div>
    </div>
    <div class="layui-side layui-bg-black x-side">
        <div id="side-nav" class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree site-demo-nav" lay-filter="side">
                <!--					--><?php //if($is_admin==1 || in_array('User',$controller)){?>
                <!--					<li class="layui-nav-item">-->
                <!--						<a class="javascript:;" href="javascript:;">-->
                <!--							<cite>会员管理</cite>-->
                <!--						</a>-->
                <!--						--><?php //if($is_admin==1 || in_array('user_list',$action)){?>
                <!--						<dl class="layui-nav-child">-->
                <!--							<dd class="">-->
                <!--								<a href="javascript:;" _href="/xiamenyyhoutai/user/user_list">-->
                <!--									<cite>会员列表</cite>-->
                <!--								</a>-->
                <!--							</dd>-->
                <!--						</dl>-->
                <!--						--><?php //} ?>
                <!--					</li>-->
                <!--					--><?php //} ?>
                <?php if ($is_admin == 1 || in_array('1', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>系统</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/admin/index">
                                        <cite>管理员</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('group_list', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/admin/group_list">
                                        <cite>管理员角色</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('auth_list', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/admin/auth_list">
                                        <cite>管理员权限</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('dict_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/dict_index">
                                        <cite>数字字典</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('building_city_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_city_index">
                                        <cite>城市管理</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('building_report_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/one_click_copy_index">
                                        <cite>一键复制开关</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('setting_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxsetting/setting_index">
                                        <cite>基础配置</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if ($is_admin == 1 || in_array('4', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>工作人员</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('company_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/company_index">
                                        <cite>组织架构</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('staff_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/staff_index">
                                        <cite>管理层</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>

                <?php if ($is_admin == 1 || in_array('8', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>项目负责</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('building_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_index">
                                        <cite>楼盘管理</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('building_report_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_report_index">
                                        <cite>报备列表</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if ($is_admin == 1 || in_array('9', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>渠道</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('store_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/store_index">
                                        <cite>店铺管理</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('agent_list_page', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/agent_list_index">
                                        <cite>经纪人信息</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('building_report_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_report_index">
                                        <cite>报备列表</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if ($is_admin == 1 || in_array('10', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>项目驻场</cite>
                        </a>

                        <?php if ($is_admin == 1 || in_array('building_report_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_report_index">
                                        <cite>报备列表</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if ($is_admin == 1 || in_array('6', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>财务</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('building_settlement_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;"
                                       _href="/xiamenyyhoutai/xcxbuilding/building_settlement_index">
                                        <cite>结佣情况</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('commission_change_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/commission_change_index">
                                        <cite>佣金修改记录</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>

                <?php if ($is_admin == 1 || in_array('7', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>用户</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('user_list', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/user/user_list">
                                        <cite>用户列表</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('suggest_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/suggest_index">
                                        <cite>意见反馈</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('collection_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/collection_index">
                                        <cite>楼盘收藏</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <!--						--><?php //if($is_admin==1 || in_array('browsing_history_index',$action)){?>
                        <!--                        <dl class="layui-nav-child">-->
                        <!--                            <dd class="">-->
                        <!--                                <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/browsing_history_index">-->
                        <!--                                    <cite>用户浏览记录</cite>-->
                        <!--                                </a>-->
                        <!--                            </dd>-->
                        <!--                        </dl>-->
                        <!--						--><?php //} ?>
                        <!--						--><?php //if($is_admin==1 || in_array('building_correct_index',$action)){?>
                        <!--                        <dl class="layui-nav-child">-->
                        <!--                            <dd class="">-->
                        <!--                                <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/building_correct_index">-->
                        <!--                                    <cite>用户楼盘纠错反馈</cite>-->
                        <!--                                </a>-->
                        <!--                            </dd>-->
                        <!--                        </dl>-->
                        <!--						--><?php //} ?>
                        <?php if ($is_admin == 1 || in_array('statistics_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/statistics_index">
                                        <cite>数据统计</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('chatting_records_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/chatting_records_index">
                                        <cite>聊天分析</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <!--						--><?php //if($is_admin==1 || in_array('announcement_inform_index',$action)){?>
                        <!--                        <dl class="layui-nav-child">-->
                        <!--                            <dd class="">-->
                        <!--                                <a href="javascript:;" _href="/xiamenyyhoutai/xcxambitus/announcement_inform_index">-->
                        <!--                                    <cite>公告通知</cite>-->
                        <!--                                </a>-->
                        <!--                            </dd>-->
                        <!--                        </dl>-->
                        <!--						--><?php //} ?>
                    </li>
                <?php } ?>

                <?php if ($is_admin == 1 || in_array('2', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>文章</cite>
                        </a>
                        <?php if ($is_admin == 1 || in_array('figure_index', $action)) { ?>
                            <!--<dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxarticle/figure_index">
                                        <cite>首页轮播图</cite>
                                    </a>
                                </dd>
                            </dl>-->
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('column_index', $action)) { ?>
                            <!--<dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxarticle/column_index">
                                        <cite>栏目管理</cite>
                                    </a>
                                </dd>
                            </dl>-->
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('article_index', $action)) { ?>
                            <!--<dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxarticle/article_index">
                                        <cite>文章管理</cite>
                                    </a>
                                </dd>
                            </dl>-->
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('comments_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxarticle/comments_index">
                                        <cite>新闻评论</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                        <?php if ($is_admin == 1 || in_array('advertising_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxarticle/advertising_index">
                                        <cite>新闻广告</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if ($is_admin == 1 || in_array('3', $menu)) { ?>
                    <li class="layui-nav-item">
                        <a class="javascript:;" href="javascript:;">
                            <cite>店铺</cite>
                        </a>
                        <!--						--><?php //if($is_admin==1 || in_array('user_index',$action)){?>
                        <!--						<dl class="layui-nav-child">-->
                        <!--							<dd class="">-->
                        <!--								<a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/user_index">-->
                        <!--									<cite>客户列表</cite>-->
                        <!--								</a>-->
                        <!--							</dd>-->
                        <!--						</dl>-->
                        <!--						--><?php //} ?>
                        <!--						--><?php //if($is_admin==1 || in_array('agent_index',$action)){?>
                        <!--						<dl class="layui-nav-child">-->
                        <!--							<dd class="">-->
                        <!--								<a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/agent_index">-->
                        <!--									<cite>用户管理</cite>-->
                        <!--								</a>-->
                        <!--							</dd>-->
                        <!--						</dl>-->
                        <!--						--><?php //} ?>

                        <?php if ($is_admin == 1 || in_array('agent_customer_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxstore/agent_customer_index">
                                        <cite>名下客户</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>



                        <?php if ($is_admin == 1 || in_array('agent_building_index', $action)) { ?>
                            <dl class="layui-nav-child">
                                <dd class="">
                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/agent_building_index">
                                        <cite>楼盘开通</cite>
                                    </a>
                                </dd>
                            </dl>
                        <?php } ?>
                    </li>
                <?php } ?>

<!--                --><?php //if ($is_admin == 1 || in_array('5', $menu)) { ?>
<!--                    <li class="layui-nav-item">-->
<!--                        <a class="javascript:;" href="javascript:;">-->
<!--                            <cite>楼盘</cite>-->
<!--                        </a>-->
<!--                        --><?php //if ($is_admin == 1 || in_array('building_index', $action)) { ?>
<!--                            <dl class="layui-nav-child">-->
<!--                                <dd class="">-->
<!--                                    <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_index">-->
<!--                                        <cite>楼盘管理</cite>-->
<!--                                    </a>-->
<!--                                </dd>-->
<!--                            </dl>-->
<!--                        --><?php //} ?>
<!--                        <!--						-->--><?php ////if($is_admin==1 || in_array('building_circularize_index',$action)){?>
<!--                        <!--                        <dl class="layui-nav-child">-->-->
<!--                        <!--                            <dd class="">-->-->
<!--                        <!--                                <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/building_circularize_index">-->-->
<!--                        <!--                                    <cite>楼盘通知</cite>-->-->
<!--                        <!--                                </a>-->-->
<!--                        <!--                            </dd>-->-->
<!--                        <!--                        </dl>-->-->
<!--                        <!--						-->--><?php ////} ?>
<!--                        <!--						-->--><?php ////if($is_admin==1 || in_array('agent_share_record_index',$action)){?>
<!--                        <!--                        <dl class="layui-nav-child">-->-->
<!--                        <!--                            <dd class="">-->-->
<!--                        <!--                                <a href="javascript:;" _href="/xiamenyyhoutai/xcxbuilding/agent_share_record_index">-->-->
<!--                        <!--                                    <cite>经纪人分享记录</cite>-->-->
<!--                        <!--                                </a>-->-->
<!--                        <!--                            </dd>-->-->
<!--                        <!--                        </dl>-->-->
<!--                        <!--						-->--><?php ////} ?>
<!--                    </li>-->
<!--                --><?php //} ?>


            </ul>
        </div>
    </div>
    <div class="layui-tab layui-tab-card site-demo-title x-main" lay-filter="x-tab" lay-allowclose="true">
        <div class="x-slide_left"></div>
        <ul class="layui-tab-title">
            <li class="layui-this">
                首页
                <i class="layui-icon layui-unselect layui-tab-close">ဆ</i>
            </li>
        </ul>
        <div class="layui-tab-content site-demo site-demo-body">
            <div class="layui-tab-item layui-show">
                <iframe frameborder="0" src="/xiamenyyhoutai/main/welcome" class="x-iframe"></iframe>
            </div>
        </div>
    </div>
    <div class="site-mobile-shade">
    </div>
</div>
<script src="/public/js/layui/layui.js" charset="utf-8"></script>
<script src="/public/js/admin/x-admin.js"></script>
<script>
    setInterval(function () {
        $.ajax({
            url: '/xiamenyyhoutai/xcxbuilding/timingTrigger',
            success: function(data){
                console.log('timingTrigger');
            }
        })
    }, 180 * 1000);
</script>
</body>
</html>