SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `url_test`
--

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

--
-- 转存表中的数据 `oreo_admin_log`
--

INSERT INTO `oreo_admin_log` (`id`, `admin_id`, `route`, `msg`, `ip_addr`, `add_time`) VALUES
(1, 1, 'admin.Login.logout', '退出登录', '42.90.158.231', '2021-03-13 23:37:01');

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
(1, 1, 'admin', 'f30c71bdf768c27f6ba40107193f0703', 1, 'Oreo饼干', '18812345678', '609451870@qq.com', 1, '2020-12-28 18:05:42', '2021-03-13 23:30:49');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_auth_menu`
--

CREATE TABLE `oreo_auth_menu` (
  `id` int NOT NULL COMMENT '主键',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `app_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应用名称',
  `action_name` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `function_name` varchar(32) NOT NULL DEFAULT '' COMMENT '方法名称',
  `user_id` int NOT NULL DEFAULT '1' COMMENT '创建人',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父级菜单,默认根菜单',
  `is_menu` int NOT NULL DEFAULT '1' COMMENT '是否显示在菜单上（0：不显示在菜单上，1：显示在菜单上。默认显示在菜单上）',
  `spread` int DEFAULT NULL COMMENT '1=>展开;0=>不展开',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `sort` int NOT NULL DEFAULT '1' COMMENT '排序',
  `icon` varchar(64) DEFAULT NULL COMMENT '图标'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表 ';

--
-- 转存表中的数据 `oreo_auth_menu`
--

INSERT INTO `oreo_auth_menu` (`id`, `name`, `app_name`, `action_name`, `function_name`, `user_id`, `parent_id`, `is_menu`, `spread`, `create_time`, `update_time`, `sort`, `icon`) VALUES
(1, '权限管理', 'admin', '/', '/', 1, 0, 1, 1, '2020-12-28 01:29:13', '2021-03-01 14:00:25', 5, 'layui-icon-vercode'),
(2, '权限规则', 'admin', 'Auth', 'permissionList', 1, 1, 1, 0, '2020-12-28 01:31:55', '2021-02-23 02:53:43', 2, ''),
(3, '查看权限规则', 'admin', 'Auth', 'adminMenuList', 1, 2, 0, 0, '2020-12-28 01:35:16', '2021-02-23 02:53:54', 0, ''),
(4, '添加权限规则', 'admin', 'Auth', 'addAdminMenuData', 1, 2, 0, 0, '2020-12-28 01:35:49', '2021-02-23 02:53:54', 1, ''),
(5, '删除权限规则', 'admin', 'Auth', 'delPermissionData', 1, 2, 0, 0, '2020-12-28 01:41:41', '2021-02-23 02:53:58', 2, ''),
(6, '角色管理', 'admin', 'Auth', 'adminRole', 1, 1, 1, 0, '2020-12-28 09:25:28', '2021-02-23 02:53:42', 1, ''),
(7, '查看角色列表', 'admin', 'Auth', 'adminRoleList', 1, 6, 0, 0, '2020-12-28 14:11:15', '2021-02-23 02:53:47', 0, ''),
(8, '添加角色', 'admin', 'Auth', 'addAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:54:07', '2021-02-23 02:53:49', 1, ''),
(9, '编辑角色', 'admin', 'Auth', 'editAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:54:43', '2021-02-23 02:53:50', 2, ''),
(10, '删除角色', 'admin', 'Auth', 'delAdminRoleData', 1, 6, 0, 0, '2020-12-28 14:55:30', '2021-02-23 02:53:51', 3, ''),
(12, '设置角色权限', 'admin', 'Auth', 'addRolePermissionData', 1, 6, 0, 0, '2020-12-28 14:59:50', '2021-02-23 02:53:51', 5, ''),
(13, '用户管理', 'admin', 'Auth', 'adminList', 1, 1, 1, 0, '2020-12-28 15:00:46', '2021-02-23 02:53:35', 0, ''),
(14, '查看管理员用户列表', 'admin', 'Auth', 'adminUserList', 1, 13, 0, 0, '2020-12-28 15:01:50', '2021-02-23 02:53:36', 0, ''),
(16, '添加管理员账号', 'admin', 'Auth', 'addAdminData', 1, 13, 0, 0, '2020-12-28 15:03:07', '2021-02-23 02:53:38', 2, ''),
(17, '编辑管理员账号', 'admin', 'Auth', 'editAdminData', 1, 13, 0, 0, '2020-12-28 15:03:32', '2021-02-23 02:53:39', 3, ''),
(18, '删除管理员账号', 'admin', 'Auth', 'delAdminData', 1, 13, 0, 0, '2020-12-28 15:04:01', '2021-02-23 02:53:41', 4, ''),
(19, '首页', 'admin', '', '', 1, 0, 1, 0, '2020-12-28 15:26:05', '2021-02-23 04:43:35', 1, 'layui-icon-console'),
(20, '系统设置', 'admin', '/', '/', 1, 0, 1, 0, '2020-12-29 10:32:41', '2021-02-23 04:43:37', 2, 'layui-icon-set-sm'),
(22, '基本参数', 'admin', 'System', 'webSite', 1, 20, 1, 0, '2020-12-29 14:48:04', '2021-02-23 04:43:45', 1, ''),
(23, '编辑权限', 'admin', 'System', 'systemSet', 1, 20, 0, 0, '2020-12-29 16:51:04', '2021-02-23 04:43:45', 2, ''),
(30, '编辑权限规则', 'admin', 'Auth', 'editAdminMenuData', 1, 2, 0, 0, '2021-02-23 03:35:03', '2021-02-23 03:35:03', 3, ''),
(31, '域名配置', 'admin', '/', '/', 1, 0, 1, 0, '2021-02-23 04:34:52', '2021-02-23 04:43:38', 3, 'layui-icon-website'),
(32, '域名列表', 'admin', 'Domain', 'domainConfig', 1, 31, 1, 0, '2021-02-23 04:35:33', '2021-02-23 04:43:56', 1, ''),
(33, '查看域名列表', 'admin', 'Domain', 'domainList', 1, 32, 0, 0, '2021-02-23 04:41:30', '2021-02-23 04:43:58', 1, ''),
(34, '添加域名', 'admin', 'Domain', 'addDomainData', 1, 32, 0, 0, '2021-02-23 04:42:40', '2021-02-23 04:43:59', 2, ''),
(35, '编辑域名', 'admin', 'Domain', 'editDomainData', 1, 32, 0, 0, '2021-02-23 04:42:56', '2021-02-23 04:44:00', 3, ''),
(36, '删除域名', 'admin', 'Domain', 'delDomainData', 1, 32, 0, 0, '2021-02-23 04:43:11', '2021-02-23 04:44:01', 4, ''),
(37, '短连接列表', 'admin', 'Domain', 'shortDomain', 1, 31, 1, 0, '2021-02-23 04:46:32', '2021-02-23 04:46:32', 2, ''),
(38, '查看短链列表', 'admin', 'Domain', 'shortDomainList', 1, 37, 0, 0, '2021-02-23 04:47:31', '2021-02-23 04:47:31', 1, ''),
(39, '短链续签', 'admin', 'Domain', 'ShortDomainTime', 1, 37, 0, 0, '2021-02-23 04:47:44', '2021-02-23 04:47:44', 2, ''),
(40, '删除短链', 'admin', 'Domain', 'delShortDomain', 1, 37, 0, 0, '2021-02-23 04:47:59', '2021-02-23 04:47:59', 3, ''),
(41, '删除全部失效短链', 'admin', 'Domain', 'delAllShortDomain', 1, 37, 0, 0, '2021-02-23 04:48:16', '2021-02-23 04:48:16', 4, ''),
(42, '域名过滤设置', 'admin', 'Domain', 'shortUrlFilter', 1, 31, 1, 0, '2021-02-23 18:24:04', '2021-02-23 18:24:04', 3, ''),
(43, '查看过滤列表', 'admin', 'Domain', 'shortUrlFilterList', 1, 42, 0, 0, '2021-02-23 18:35:31', '2021-02-23 18:35:31', 1, ''),
(44, '添加域名过滤', 'admin', 'Domain', 'addUrlFilter', 1, 42, 0, 0, '2021-02-23 18:46:43', '2021-02-23 18:46:43', 2, ''),
(45, '编辑域名过滤', 'admin', 'Domain', 'editUrlFilter', 1, 42, 0, 0, '2021-02-23 18:55:43', '2021-02-23 18:55:43', 3, ''),
(46, '删除域名过滤', 'admin', 'Domain', 'delUrlFilter', 1, 42, 0, 0, '2021-02-23 19:01:45', '2021-02-23 19:01:45', 4, ''),
(47, '平台用户', 'admin', '/', '/', 1, 0, 1, 0, '2021-02-24 07:40:46', '2021-02-24 07:41:09', 4, 'layui-icon-username'),
(48, '用户列表', 'admin', 'User', 'userList', 1, 47, 1, 0, '2021-02-24 07:41:36', '2021-02-24 07:41:36', 1, ''),
(49, '邮箱/短信设置', 'admin', 'System', 'send', 1, 20, 1, 0, '2021-03-02 04:16:51', '2021-03-02 04:16:51', 3, ''),
(50, '查看域名列表', 'admin', 'User', 'userDataList', 1, 48, 0, 0, '2021-03-03 07:15:00', '2021-03-03 07:15:00', 1, ''),
(51, '添加用户', 'admin', 'User', 'addUserData', 1, 48, 0, 0, '2021-03-03 07:15:24', '2021-03-03 07:15:24', 2, ''),
(52, '编辑用户', 'admin', 'User', 'editUserData', 1, 48, 0, 0, '2021-03-03 07:15:46', '2021-03-03 07:15:46', 3, ''),
(53, '删除用户', 'admin', 'User', 'delUserData', 1, 48, 0, 0, '2021-03-03 07:16:06', '2021-03-03 07:16:06', 4, '');

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
(27, 1, 19, '2021-02-23 03:30:38', '2021-02-23 03:30:38'),
(28, 1, 20, '2021-02-23 03:30:38', '2021-02-23 03:30:38'),
(29, 1, 22, '2021-02-23 03:30:39', '2021-02-23 03:30:39'),
(30, 1, 23, '2021-02-23 03:30:39', '2021-02-23 03:30:39'),
(31, 1, 1, '2021-02-23 03:30:39', '2021-02-23 03:30:39'),
(32, 1, 13, '2021-02-23 03:30:40', '2021-02-23 03:30:40'),
(33, 1, 14, '2021-02-23 03:30:40', '2021-02-23 03:30:40'),
(34, 1, 16, '2021-02-23 03:30:41', '2021-02-23 03:30:41'),
(35, 1, 17, '2021-02-23 03:30:41', '2021-02-23 03:30:41'),
(36, 1, 18, '2021-02-23 03:30:41', '2021-02-23 03:30:41'),
(37, 1, 6, '2021-02-23 03:30:42', '2021-02-23 03:30:42'),
(38, 1, 7, '2021-02-23 03:30:42', '2021-02-23 03:30:42'),
(39, 1, 8, '2021-02-23 03:30:42', '2021-02-23 03:30:42'),
(40, 1, 9, '2021-02-23 03:30:43', '2021-02-23 03:30:43'),
(41, 1, 10, '2021-02-23 03:30:44', '2021-02-23 03:30:44'),
(42, 1, 12, '2021-02-23 03:30:46', '2021-02-23 03:30:46'),
(43, 1, 2, '2021-02-23 03:30:46', '2021-02-23 03:30:46'),
(44, 1, 3, '2021-02-23 03:30:46', '2021-02-23 03:30:46'),
(45, 1, 4, '2021-02-23 03:30:47', '2021-02-23 03:30:47'),
(46, 1, 5, '2021-02-23 03:30:47', '2021-02-23 03:30:47'),
(47, 1, 30, '2021-02-23 03:35:27', '2021-02-23 03:35:27'),
(80, 1, 31, '2021-02-23 04:35:47', '2021-02-23 04:35:47'),
(81, 1, 32, '2021-02-23 04:35:48', '2021-02-23 04:35:48'),
(82, 1, 33, '2021-02-23 04:44:12', '2021-02-23 04:44:12'),
(86, 1, 34, '2021-02-23 04:46:06', '2021-02-23 04:46:06'),
(87, 1, 35, '2021-02-23 04:46:06', '2021-02-23 04:46:06'),
(88, 1, 36, '2021-02-23 04:46:06', '2021-02-23 04:46:06'),
(89, 1, 37, '2021-02-23 04:48:31', '2021-02-23 04:48:31'),
(94, 1, 38, '2021-02-23 04:51:31', '2021-02-23 04:51:31'),
(95, 1, 39, '2021-02-23 04:51:46', '2021-02-23 04:51:46'),
(96, 1, 40, '2021-02-23 04:51:46', '2021-02-23 04:51:46'),
(97, 1, 41, '2021-02-23 04:51:46', '2021-02-23 04:51:46'),
(98, 1, 42, '2021-02-23 18:36:27', '2021-02-23 18:36:27'),
(99, 1, 43, '2021-02-23 18:36:28', '2021-02-23 18:36:28'),
(100, 1, 44, '2021-02-23 18:46:59', '2021-02-23 18:46:59'),
(101, 1, 45, '2021-02-23 18:55:51', '2021-02-23 18:55:51'),
(109, 1, 47, '2021-02-24 07:44:26', '2021-02-24 07:44:26'),
(110, 1, 48, '2021-02-24 07:44:28', '2021-02-24 07:44:28'),
(111, 1, 49, '2021-03-02 04:17:27', '2021-03-02 04:17:27'),
(121, 1, 50, '2021-03-03 07:16:19', '2021-03-03 07:16:19'),
(122, 1, 51, '2021-03-03 07:16:19', '2021-03-03 07:16:19'),
(123, 1, 52, '2021-03-03 07:16:19', '2021-03-03 07:16:19'),
(124, 1, 53, '2021-03-03 07:16:20', '2021-03-03 07:16:20');

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
(1, '超级管理员', 1, '此权限为系统最高权限，且不可删除 ', '2020-12-28 18:05:19', '2021-03-05 17:16:04');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_domain`
--

CREATE TABLE `oreo_domain` (
  `id` int NOT NULL COMMENT '唯一ID',
  `filter_id` int NOT NULL COMMENT '过滤组ID',
  `domain` varchar(128) NOT NULL COMMENT '域名',
  `cycle` int NOT NULL COMMENT '生命周期,天为单位，0则不限',
  `safe` int NOT NULL DEFAULT '2' COMMENT '防红;1=>开;2=>关闭',
  `safe_tpl` varchar(18) DEFAULT NULL COMMENT '模板名称',
  `state` tinyint NOT NULL COMMENT '状态;1=>正常生成;2=>登录后生成;3=>停止服务',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统域名列表';

-- --------------------------------------------------------

--
-- 表的结构 `oreo_domain_filter`
--

CREATE TABLE `oreo_domain_filter` (
  `id` int NOT NULL COMMENT '唯一ID',
  `filter_name` varchar(32) NOT NULL COMMENT '名称',
  `filter_content` longtext NOT NULL COMMENT '过滤内容',
  `create_time` datetime NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='域名过滤设置表';

--
-- 转存表中的数据 `oreo_domain_filter`
--

INSERT INTO `oreo_domain_filter` (`id`, `filter_name`, `filter_content`, `create_time`) VALUES
(1, '全部放行', 'null', '2021-02-23 18:47:20'),
(2, '过滤海外', 'yahoo|amazon|google|pornhub|xnxx|github|porn|tube', '2021-02-23 19:11:25');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_short_url`
--

CREATE TABLE `oreo_short_url` (
  `id` int NOT NULL COMMENT '唯一ID',
  `domain_id` int NOT NULL COMMENT '域名ID',
  `user_id` int DEFAULT NULL COMMENT '用户ID',
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
-- 表的结构 `oreo_short_url_log`
--

CREATE TABLE `oreo_short_url_log` (
  `id` int NOT NULL COMMENT '唯一ID',
  `short_id` int NOT NULL COMMENT '短网址ID',
  `user_id` int DEFAULT NULL COMMENT '用户ID',
  `lang` tinyint NOT NULL COMMENT '语言',
  `browser` tinyint NOT NULL COMMENT '浏览器',
  `system` tinyint NOT NULL COMMENT '系统',
  `ip_address` varchar(20) DEFAULT NULL COMMENT '客户端IP',
  `city` varchar(32) DEFAULT NULL COMMENT '客户端城市',
  `add_time` datetime NOT NULL COMMENT '浏览时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短网址日志';

-- --------------------------------------------------------

--
-- 表的结构 `oreo_sms_log`
--

CREATE TABLE `oreo_sms_log` (
  `id` int NOT NULL COMMENT '唯一ID',
  `type` tinyint NOT NULL COMMENT '1=>邮件;2=>短信',
  `target` varchar(128) NOT NULL COMMENT '接受目标',
  `code` int NOT NULL COMMENT '6位数字验证码',
  `state` tinyint NOT NULL COMMENT '1=>已验证;2=>未验证',
  `client_ip` varchar(20) DEFAULT NULL COMMENT '客户端IP',
  `send_time` int NOT NULL COMMENT '发送时间',
  `verify_time` int DEFAULT NULL COMMENT '接受时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='验证码发送日志';

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
(1, '网站名称', 'web_name', '饼干短网址'),
(2, '网站域名', 'web_url', 'http://www.2free.cn'),
(3, '工信部备案号', 'icp_num', '黔ICP备2020009901号'),
(4, '系统状态', 'system_state', '1'),
(5, '系统维护提示语', 'system_state_text', '系统维护中，请稍后再试.....'),
(6, 'SMTP地址', 'smtp_url', 'smtp.qq.com'),
(7, 'SMTP端口', 'smtp_port', '465'),
(8, '发件邮箱账号', 'mail_name', '609451870@qq.com'),
(9, '发件邮箱密码', 'mail_pass', '123456'),
(10, 'AccessKeyId', 'sms_ali_id', 'AAABBBCCCDDDXXXX'),
(11, 'AccessKeySecret', 'sms_ali_secret', 'aAbBDdCcXxYyZz'),
(12, '签名内容', 'sms_ali_sign_name', '奥利奥'),
(13, '短信模板ID', 'sms_ali_tpl_id', 'SMS_20123456'),
(14, 'AppId', 'sms_ten_id', '14001234567'),
(15, 'AppKey', 'sms_ten_key', 'aAbBDdCcXxYyZz'),
(16, '模板ID', 'sms_ten_tpl_id', '123456'),
(17, '短信签名', 'sms_ten_sign', '奥利奥'),
(18, '验证码获取方式', 'system_sms', '3');

-- --------------------------------------------------------

--
-- 表的结构 `oreo_user`
--

CREATE TABLE `oreo_user` (
  `id` int NOT NULL COMMENT '唯一ID',
  `password` varchar(64) NOT NULL COMMENT '登录密码',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(18) DEFAULT NULL COMMENT '手机号码',
  `qq_num` varchar(20) DEFAULT NULL COMMENT 'QQ号码',
  `login_ip` varchar(25) DEFAULT NULL COMMENT '登录IP',
  `state` tinyint NOT NULL COMMENT '状态;1=>正常;2=>封禁',
  `login_time` datetime DEFAULT NULL COMMENT '最近登录时间',
  `create_time` datetime NOT NULL COMMENT '注册时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- 表的索引 `oreo_domain_filter`
--
ALTER TABLE `oreo_domain_filter`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `oreo_short_url`
--
ALTER TABLE `oreo_short_url`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain_id` (`domain_id`),
  ADD KEY `address` (`address`);

--
-- 表的索引 `oreo_short_url_log`
--
ALTER TABLE `oreo_short_url_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `short_id` (`short_id`);

--
-- 表的索引 `oreo_sms_log`
--
ALTER TABLE `oreo_sms_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`);

--
-- 表的索引 `oreo_system`
--
ALTER TABLE `oreo_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- 表的索引 `oreo_user`
--
ALTER TABLE `oreo_user`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `oreo_admin_log`
--
ALTER TABLE `oreo_admin_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_auth_admin`
--
ALTER TABLE `oreo_auth_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_auth_menu`
--
ALTER TABLE `oreo_auth_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=54;

--
-- 使用表AUTO_INCREMENT `oreo_auth_permission`
--
ALTER TABLE `oreo_auth_permission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=126;

--
-- 使用表AUTO_INCREMENT `oreo_auth_role`
--
ALTER TABLE `oreo_auth_role`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `oreo_domain`
--
ALTER TABLE `oreo_domain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_domain_filter`
--
ALTER TABLE `oreo_domain_filter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `oreo_short_url`
--
ALTER TABLE `oreo_short_url`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_short_url_log`
--
ALTER TABLE `oreo_short_url_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_sms_log`
--
ALTER TABLE `oreo_sms_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';

--
-- 使用表AUTO_INCREMENT `oreo_system`
--
ALTER TABLE `oreo_system`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID', AUTO_INCREMENT=19;

--
-- 使用表AUTO_INCREMENT `oreo_user`
--
ALTER TABLE `oreo_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '唯一ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
