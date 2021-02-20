# 【饼干短链接生成系统（基于OreoFrame框架）】

> #### 手册阅读须知：本手册仅针对 饼干短链接生成系统`1.0.*`版本
>
> *******************************************************************************

> **本系统是自豪的采用OreoFrame微PHP快速开发框架1.0的基础上开发出来的短连接生成系统。**
>
> 系统的开发完全遵循OreoFrame的快速开发理念以及灵活特征打造的产品，同样本系统也能作为学习OreoFrame的一套完整教程。
>

## 一、管理员后台功能（包括且不限于）：

1.完整的基于角色的访问控制（*RBAC*）模块。

2.系统基本参数的设置。

3.短链接域名的配置，包括但不限于（多条域名的增删改查），生命周期设置，防红设置，防红模板选择，状态设置。

4.生成的短链接点击统计功能。



## 二、前台

1.简洁大气的生成页面。

2.自动获取后台设置的短链接域名。

3.自动生成且sweetalert返回。



## 三、生成与访问的实现

整个逻辑过程：

用户提交短链接生成请求 -> 后端验证是否合法请求 -> 自动判断是否存在系统库短链接域名 -> 判断是否协议域名 -> 查询是否已存在链接 -> 更新或新增处理->记录生成者的IP地址 -> 冗余校验码分配地址 -> 返回。

访问： 根据地址块查找 -> 判断是否合法 -> 通过验证判断是否原短链接地址 -> 其他记录 -> 跳转。

其实很多功能的实现比描述的复杂的多且有很多验证机制，如果你是好学可以参考和学习且可以与作者一起探讨。



## 四、安装

>  **开发环境：PHP8 ，MySql8.0，Redis6.0**
>
> **运行环境：PHP7+，Mysql8.0，Reids可根据业务需求安装和配置即可**

注:关于数据库的说明：原则上最低数据库版本为8.0，若您的环境无此版本，则最低要求5.7。

```git
git clone https://github.com/emran519/oreo_shortUrl.git
```

你可以直接克隆到项目文件，或者打包下载到本地。

框架需要配置伪静态规则：

Apache

```apache
RewriteEngine on 
RewriteCond %{REQUEST_FILENAME} !-d 
RewriteCond %{REQUEST_FILENAME} !-f 
RewriteRule ^(.*)$ index.php/$1 [L] 
```

Nginx

```Nginx
if (!-d $request_filename){
	set $rule_0 1$rule_0;
}
if (!-f $request_filename){
	set $rule_0 2$rule_0;
}
if ($rule_0 = "21"){
	rewrite ^/(.*)$ /index.php/$1 last;
}
```

```
运行目录设置为： /public
```

**导入数据库文件以及配置数据库链接文件**

在根目录中获取**database.sql**文件，导入至mysql，而后编辑 **根目录/config/oreo_database.php** 文件中的相关配置信息

注: redis以及其他项配置在 **根目录/config/oreo_app.php** 文件中，如需配置Redis也请在此文件中设置（redis开启后您可以选择开启） 

**redis开启后**您可以选择开启 **安全监测**(如果触犯安全规则IP会被暂时拉黑，此功能需开启redis)，redis未配置请把 safety 设为 false



## 五、后台地址

> http://你的URL/home
>
> 默认登录帐号：admin
>
> 默认登录密码：123465