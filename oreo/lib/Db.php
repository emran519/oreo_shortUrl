<?php

namespace oreo\lib;

use oreo\lib\db\Query;

/**
 * Class Db 数据库驱动
 * @package oreo\lib\db
 * @version  1.0
 * Author: oreo <609451870@qq.com>
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 */
class Db {
    public static function table($table = '') {
        return new Query($table);
    }

    public static function query($sql){
        $q = new Query("");
        return $q->query($sql);
    }

    /**
     * 获取上一次插入id
     * @return int
     */
    public static function lastInsertId() {
        return (new Query(''))->lastInsertId();
    }

    /**
     * 开始事务
     */
    public static function begin() {
        (new Query(''))->begin();
    }

    /**
     * 提交事务
     */
    public static function commit() {
        (new Query(''))->commit();
    }

    /**
     * 回滚事务
     */
    public static function rollback() {
        (new Query(''))->rollback();
    }
}
