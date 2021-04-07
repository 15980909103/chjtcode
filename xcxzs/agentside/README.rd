# 九房


## 入口
```
h5: agentside/index.html
小程序: xcx/
```


## 前端配置文件
```
agentside/static/js/public.js?v=2
```


## 后端数据库配置文件
```
setting/local.php
```


## 后端登录控制器
```
# module/agentapi/controller/common.php
# __construct

    public function __construct(){
       Session::set('agent_id', 1);
       Session::set('said',1);
       ...
```


## 包

- mui

```
https://dev.dcloud.net.cn/mui/ui/
```
- mui.pullToRefresh

```
https://ask.dcloud.net.cn/article/1215
```
- jquery

```
https://api.jquery.com/
```
- swiper

```
https://www.swiper.com.cn/api/index.html
```


## 结构

| 文件名 | 说明 |
| ----- | ---- |
| components | 组件 |
| mixins | 混入 |
| pages | 页面(非单文件) |
| static | 静态资源 |

位于根目录则为基础路径

## 访问

nginx 或者 apache,没有修改hosts则需要设置代理并修改前端控制文件地址.
