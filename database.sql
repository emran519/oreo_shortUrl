-- 主机： localhost
-- 生成日期： 2021-02-21 02:07:52
-- 服务器版本： 8.0.20
-- PHP 版本： 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- 表的结构 `oreo_admin_log`
--

CREATE TABLE `oreo_admin_log` (
  `id` int NOT NULL COMMENT '唯一ID',
  `admin_id` int DEFAULT NULL COMMENT '管理员ID',
  `route` text COMMENT '路由',
  `msg` text NOT NULL COMMENT '内容',
  `ip_addr` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '用户IP',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oreo_auth_admin`
--

CREATE TABLE `oreo_auth_admin` (
  `id` int NOT NULL COMMENT '唯一ID',
  `role_id` int NOT NULL COMMENT '权限ID',
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `gender` tinyint NOT NULL COMMENT '1=>男;2=>女',
  `real_name` varchar(125) DEFAULT NULL COMMENT '真实姓名',
  `user_phone` varchar(18) DEFAULT NULL COMMENT '手机号码',
  `user_email` varchar(125) DEFAULT NULL COMMENT '用户邮箱',
  `state` tinyint NOT NULL COMMENT '1=>正常;2=>封禁',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `last_login_time` datetime DEFAULT NULL COMMENT '最近登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `oreo_auth_admin`
--

INSERT INTO `oreo_auth_admin` (`id`, `role_id`, `username`, `password`, `gender`, `real_name`, `user_phone`, `user_email`, `state`, `create_time`, `last_login_time`) VALUES
(1, 1, 'admin', 'f30c71bdf768c27f6ba40107193f0703', 1, 'Oreo饼干', '18812345678', '609451870@qq.com', 1, '2020-12-28 18:05:42', '2021-02-20 23:35:44');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_auth_menu`
--

CREATE TABLE `oreo_auth_menu` (
  `id` int NOT NULL COMMENT '主键',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `action_name` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `function_name` varchar(32) NOT NULL DEFAULT '' COMMENT '方法名称',
  `user_id` int NOT NULL DEFAULT '1' COMMENT '创建人',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父级菜单,默认根菜单',
  `is_menu` int NOT NULL DEFAULT '1' COMMENT '是否显示在菜单上（0：不显示在菜单上，1：显示在菜单上。默认显示在菜单上）',
  `spread` int DEFAULT NULL COMMENT '1=>展开;0=>不展开',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `sort` int DEFAULT '1' COMMENT '排序',
  `icon` varchar(64) DEFAULT NULL COMMENT '图标'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表 ';

--
-- 转存表中的数据 `oreo_auth_menu`
--

INSERT INTO `oreo_auth_menu` (`id`, `name`, `action_name`, `function_name`, `user_id`, `parent_id`, `is_menu`, `spread`, `create_time`, `update_time`, `sort`, `icon`) VALUES
(1, '权限管理', '/', '/', 1, 0, 1, 1, '2020-12-28 01:29:13', '2020-12-28 01:29:13', 5, 'layui-icon-vercode'),
(2, '权限规则', 'Auth', '/permissionList', 1, 1, 1, 0, '2020-12-28 01:31:55', '2020-12-28 01:31:55', 2, ''),
(3, '查看权限规则', 'Auth', '/adminMenuList', 1, 2, 0, 0, '2020-12-28 01:35:16', '2020-12-28 01:35:16', 0, ''),
(4, '添加权限规则', 'Auth', '/addAdminMenuData', 1, 2, 0, 0, '2020-12-28 01:35:49', '2020-12-28 01:35:49', 1, ''),
(5, '删除权限规则', 'Auth', '/delPermissionData', 1, 2, 0, 0, '2020-12-28 01:41:41', '2020-12-28 01:41:41', 3, ''),
(6, '角色管理', 'Auth', '/adminRole', 1, 1, 1, 0, '2020-12-28 09:25:28', '2020-12-28 09:25:28', 1, ''),
(7, '查看角色列表', 'Auth', '/adminRoleList', 1, 6, 0, 0, '2020-12-28 14:11:15', '2020-12-28 14:11:15', 0, ''),
(8, '添加角色', 'Auth', '/addAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:54:07', '2020-12-28 14:54:07', 1, ''),
(9, '编辑角色', 'Auth', '/editAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:54:43', '2020-12-28 14:54:43', 2, ''),
(10, '删除角色', 'Auth', '/delAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:55:30', '2020-12-28 14:55:30', 3, ''),
(12, '设置角色权限', 'Auth', '/addRolePermissionData', 1, 6, 0, 0, '2020-12-28 14:59:50', '2020-12-28 14:59:50', 4, ''),
(13, '用户管理', 'Auth', '/adminList', 1, 1, 1, 0, '2020-12-28 15:00:46', '2020-12-28 15:00:46', 0, ''),
(14, '查看管理员用户列表', 'Auth', '/adminUserList', 1, 13, 0, 0, '2020-12-28 15:01:50', '2020-12-28 15:01:50', 0, ''),
(16, '添加管理员账号', 'Auth', '/addAdminData', 1, 13, 0, 0, '2020-12-28 15:03:07', '2020-12-28 15:03:07', 1, ''),
(17, '编辑管理员账号', 'Auth', '/editAdminData', 1, 13, 0, 0, '2020-12-28 15:03:32', '2020-12-28 15:03:32', 2, ''),
(18, '删除管理员账号', 'Auth', '/delAdminData', 1, 13, 0, 0, '2020-12-28 15:04:01', '2020-12-28 15:04:01', 3, ''),
(19, '首页', 'Home', '', 1, 0, 1, 0, '2020-12-28 15:26:05', '2021-02-20 23:51:35', 0, 'layui-icon-console'),
(20, '系统设置', '/', '/', 1, 0, 1, 0, '2020-12-29 10:32:41', '2020-12-29 10:32:41', 1, 'layui-icon-set-sm'),
(22, '基本参数', 'System', '/webSite', 1, 20, 1, 0, '2020-12-29 14:48:04', '2020-12-29 14:48:04', 0, ''),
(23, '编辑权限', 'System', '/systemSet', 1, 20, 0, 0, '2020-12-29 16:51:04', '2020-12-29 16:51:04', 1, ''),
(31, '域名配置', '/', '/', 1, 0, 1, 0, '2021-02-18 03:10:38', '2021-02-18 03:10:38', 2, 'layui-icon-website'),
(32, '编辑权限规则', 'Auth', '/editAdminMenuData', 1, 2, 0, 0, '2021-02-18 03:37:12', '2021-02-18 03:37:12', 2, ''),
(33, '域名列表', 'Domain', '/domainConfig', 1, 31, 1, 0, '2021-02-18 03:41:02', '2021-02-18 03:41:02', 0, ''),
(34, '短连接列表', 'Domain', '/shortDomain', 1, 31, 1, 0, '2021-02-20 11:52:18', '2021-02-20 12:12:57', 1, ''),
(35, '查看域名列表', 'Domain', '/domainList', 1, 33, 0, 0, '2021-02-21 00:27:33', '2021-02-21 00:27:33', 0, ''),
(36, '添加域名', 'Domain', '/addDomainData', 1, 33, 0, 0, '2021-02-21 00:28:54', '2021-02-21 00:28:54', 1, ''),
(37, '编辑域名', 'Domain', '/editDomainData', 1, 33, 0, 0, '2021-02-21 00:29:19', '2021-02-21 00:29:19', 2, ''),
(38, '删除域名', 'Domain', '/delDomainData', 1, 33, 0, 0, '2021-02-21 00:29:44', '2021-02-21 00:29:44', 3, ''),
(39, '查看短链列表', 'Domain', '/shortDomainList', 1, 34, 0, 0, '2021-02-21 00:31:40', '2021-02-21 00:31:40', 0, ''),
(40, '短链续签', 'Domain', '/ShortDomainTime', 1, 34, 0, 0, '2021-02-21 00:32:22', '2021-02-21 00:32:22', 1, ''),
(41, '删除短链', 'Domain', '/delShortDomain', 1, 34, 0, 0, '2021-02-21 00:32:48', '2021-02-21 00:32:48', 2, ''),
(42, '删除全部失效短链', 'Domain', '/delAllShortDomain', 1, 34, 0, 0, '2021-02-21 00:33:10', '2021-02-21 00:33:10', 3, '');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_auth_permission`
--

CREATE TABLE `oreo_auth_permission` (
  `id` int NOT NULL COMMENT '主键',
  `user_id` int NOT NULL DEFAULT '1' COMMENT '角色ID',
  `menu_id` int NOT NULL DEFAULT '1' COMMENT '菜单ID',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';

--
-- 转存表中的数据 `oreo_auth_permission`
--

INSERT INTO `oreo_auth_permission` (`id`, `user_id`, `menu_id`, `create_time`, `update_time`) VALUES
(1, 1, 2, '2021-01-07 02:59:49', '2021-01-07 02:59:49'),
(2, 1, 3, '2021-01-07 02:59:49', '2021-01-07 02:59:49'),
(3, 1, 4, '2021-01-07 02:59:50', '2021-01-07 02:59:50'),
(4, 1, 5, '2021-01-07 02:59:50', '2021-01-07 02:59:50'),
(6, 1, 20, '2021-01-07 03:09:01', '2021-01-07 03:09:01'),
(9, 1, 12, '2021-01-07 03:09:09', '2021-01-07 03:09:09'),
(10, 1, 10, '2021-01-07 03:09:09', '2021-01-07 03:09:09'),
(11, 1, 9, '2021-01-07 03:09:10', '2021-01-07 03:09:10'),
(12, 1, 8, '2021-01-07 03:09:10', '2021-01-07 03:09:10'),
(13, 1, 7, '2021-01-07 03:09:11', '2021-01-07 03:09:11'),
(14, 1, 6, '2021-01-07 03:09:11', '2021-01-07 03:09:11'),
(15, 1, 18, '2021-01-07 03:09:11', '2021-01-07 03:09:11'),
(16, 1, 17, '2021-01-07 03:09:12', '2021-01-07 03:09:12'),
(17, 1, 16, '2021-01-07 03:09:12', '2021-01-07 03:09:12'),
(18, 1, 14, '2021-01-07 03:09:13', '2021-01-07 03:09:13'),
(19, 1, 13, '2021-01-07 03:09:14', '2021-01-07 03:09:14'),
(20, 1, 1, '2021-01-07 03:09:14', '2021-01-07 03:09:14'),
(25, 1, 22, '2021-01-07 14:33:46', '2021-01-07 14:33:46'),
(26, 1, 23, '2021-01-07 14:33:47', '2021-01-07 14:33:47'),
(29, 1, 31, '2021-02-18 03:11:14', '2021-02-18 03:11:14'),
(30, 1, 32, '2021-02-18 03:37:18', '2021-02-18 03:37:18'),
(31, 1, 33, '2021-02-18 03:41:44', '2021-02-18 03:41:44'),
(33, 1, 34, '2021-02-20 11:57:57', '2021-02-20 11:57:57'),
(34, 1, 35, '2021-02-21 00:28:10', '2021-02-21 00:28:10'),
(35, 1, 36, '2021-02-21 00:30:04', '2021-02-21 00:30:04'),
(36, 1, 37, '2021-02-21 00:30:04', '2021-02-21 00:30:04'),
(37, 1, 38, '2021-02-21 00:30:05', '2021-02-21 00:30:05'),
(38, 1, 39, '2021-02-21 00:34:19', '2021-02-21 00:34:19'),
(39, 1, 40, '2021-02-21 00:34:19', '2021-02-21 00:34:19'),
(40, 1, 41, '2021-02-21 00:34:20', '2021-02-21 00:34:20'),
(41, 1, 42, '2021-02-21 00:34:20', '2021-02-21 00:34:20'),
(43, 1, 19, '2021-02-21 02:04:06', '2021-02-21 02:04:06');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_auth_role`
--

CREATE TABLE `oreo_auth_role` (
  `id` int NOT NULL COMMENT '唯一ID',
  `role_name` varchar(32) NOT NULL DEFAULT '' COMMENT '角色名称',
  `user_id` int NOT NULL DEFAULT '1' COMMENT '创建人',
  `represent` varchar(125) DEFAULT NULL COMMENT '描述',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色表';

--
-- 转存表中的数据 `oreo_auth_role`
--

INSERT INTO `oreo_auth_role` (`id`, `role_name`, `user_id`, `represent`, `create_time`, `update_time`) VALUES
(1, '超级管理员', 1, '此权限为系统最高权限，且不可删除 ', '2020-12-28 18:05:19', '2020-12-28 18:05:19');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_domain`
--

CREATE TABLE `oreo_domain` (
  `id` int NOT NULL COMMENT '唯一ID',
  `domain` varchar(128) NOT NULL COMMENT '域名',
  `cycle` int NOT NULL COMMENT '生命周期,天为单位，0则不限',
  `safe` int NOT NULL DEFAULT '2' COMMENT '防红;1=>开;2=>关闭',
  `safe_tpl` varchar(18) DEFAULT NULL COMMENT '模板名称',
  `state` tinyint NOT NULL COMMENT '1=>正常:2=>停止',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统域名列表';

-- --------------------------------------------------------

--
-- 表的结构 `oreo_domain_text`
--

CREATE TABLE `oreo_domain_text` (
  `id` int NOT NULL COMMENT '唯一ID',
  `domain_id` int NOT NULL COMMENT '域名ID',
  `address` varchar(32) NOT NULL COMMENT '分配地址',
  `target` text NOT NULL COMMENT '目标地址',
  `cycle` int NOT NULL COMMENT '生命周期,天为单位，0则不限 	',
  `record` int DEFAULT '0' COMMENT '使用频率',
  `user_ip` varchar(25) NOT NULL COMMENT '创建者IP',
  `create_time` int NOT NULL COMMENT '添加时间',
  `end_time` int NOT NULL COMMENT '到期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oreo_system`
--

CREATE TABLE `oreo_system` (
  `id` int NOT NULL COMMENT '唯一ID',
  `info` varchar(125) DEFAULT NULL COMMENT '配置简介',
  `name` varchar(125) NOT NULL COMMENT '字段名称',
  `value` text NOT NULL COMMENT '字段值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `oreo_system`
--

INSERT INTO `oreo_system` (`id`, `info`, `name`, `value`) VALUES
(1, '网站名称', 'web_name', '基于OreoFrame框架的免费开源防洪短链接生成系统'),
(2, '网站域名', 'web_url', 'http://u.oreopay.com'),
(3, '工信部备案号', 'icp_num', '黔ICP备2020009901号'),
(4, '系统状态', 'system_state', '1'),
(5, '系统维护提示语', 'system_state_text', '基于OreoFrame框架的免费开源防洪短链接生成系统，作者QQ:609451870');

--
-- 转储表的索引
--

--
-- 表的索引 `oreo_admin_log`
--
ALTER TABLE `oreo_admin_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `oreo_auth_admin`
--
ALTER TABLE `oreo_auth_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 表的索引 `oreo_auth_menu`
--
ALTER TABLE `oreo_auth_menu`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `oreo_auth_permission`
--
ALTER TABLE `oreo_auth_permission`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `oreo_auth_role`
--
ALTER TABLE `oreo_auth_role`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `oreo_domain`
--
ALTER TABLE `oreo_domain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain` (`domain`),
  ADD KEY `domain_2` (`domain`);

--
-- 表的索引 `oreo_domain_text`
--
ALTER TABLE `oreo_domain_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain_id` (`domain_id`),
  ADD KEY `address` (`address`);

--
-- 表的索引 `oreo_system`
--
ALTER TABLE `oreo_system`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `oreo_admin_log`
--
ALTER TABLE `oreo_admin_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_auth_admin`
--
ALTER TABLE `oreo_auth_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_auth_menu`
--
ALTER TABLE `oreo_auth_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=43;

--
-- 使用表AUTO_INCREMENT `oreo_auth_permission`
--
ALTER TABLE `oreo_auth_permission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=44;

--
-- 使用表AUTO_INCREMENT `oreo_auth_role`
--
ALTER TABLE `oreo_auth_role`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_domain`
--
ALTER TABLE `oreo_domain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_domain_text`
--
ALTER TABLE `oreo_domain_text`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_system`
--
ALTER TABLE `oreo_system`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
