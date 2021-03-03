<?php

namespace oreo\lib\db;

use oreo\lib\Config;

use PDO;

class Query {
    private static $db = null;
    private static $db_type = '';
    private static $prefix = '';

    private $table = '';
    private $where = '';
    private $field = '';
    private $order = '';
    private $limit = '';
    private $join = '';

    private $lastOper = 'and';
    private $bindParam = [];

    public function __construct($table = '') {
        $dbConfig = Config::getDatabaseConfig();
        if (self::$db == null) {
            self::$db_type = $dbConfig['type'];
            $dns  = $dbConfig['type'] . ':dbname=' . $dbConfig['database'] . ';host=';
            $dns .= $dbConfig['host'] . ';port='   . $dbConfig['port'] . ';charset=utf8';
            self::$db = new PDO($dns, $dbConfig['user'], $dbConfig['password']);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$prefix = $dbConfig['prefix'];
        }
        if (!empty($table)) {
            $this->table = $dbConfig['prefix'] . str_replace('|', ',' . $dbConfig['prefix'], $table);
        }
    }
    /**
     * 条件
     * @param $field
     * @param null $value
     * @param string $operator
     * @return $this
     */
    public function where($field, $value = null, $operator = '=') {
        $this->where .= ' ' . (empty($this->where) ? '' : $this->lastOper . ' ');
        //恢复默认运算符
        $this->lastOper = 'and';
        if (is_array($field)) {
            //获取最后一个
            $keys = array_keys($field);
            foreach ($field as $key => $item) {
                if (is_string($key)) {
                    $this->where .= "`$key`$operator? ";
                    $this->bindParam[] = $item;
                    if ($key !== end($keys)) {
                        $this->where .= 'and ';
                    }
                } else if (is_numeric($key)) {
                    $this->where .= "$item";
                    if ($key !== end($keys)) {
                        $this->where .= 'and ';
                    }
                }
            }
        } else if (is_string($field)) {
            if (is_null($value)) {
                $this->where .= $field;
            } else {
                //$this->where .= " `$field`$operator?";
                $this->where .= ' ('.$this->_addChar($field,'key').$operator.'?) ';
                $this->bindParam[] =  $value;
            }
        }
        return $this;
    }

    /**
     * 字段和表名添加 `符号
     * 保证指令中使用关键字不出错 针对mysql
     * @param string $value
     * @param string $type
     * @return string
     */
    private function _addChar(string $value, string $type) {
        if ('*'==$value || false!==strpos($value,'(') || false!==strpos($value,'.') || false!==strpos($value,'`')) {
            //如果包含* 或者 使用了sql方法 则不作处理
        } elseif (false === strpos($value,'`') ) {
            if($type=='key'){
                $value = '`'.trim($value).'`';
            }else{
                $value = "'".trim($value)."'";
            }
        }
        return $value;
    }

    //给表取别名
    public function alias($str){
        $this->table .= ' AS ' . $str;
        return $this;
    }

    /**
     * @param $table
     * @param string $on
     * @param string $link 可以取值 inner，left或者right，默认值是 inner
     * @return $this
     */
    public function join($table, $on = '', $link = 'inner') {
        if (is_array($table)) {
            foreach ($table as $key => $value) {
                if (is_string($key)) {
                    $this->join .= " $link join `" . self::$prefix . $key . "` as $value " . (empty($on) ? '' : "on $on");
                } else if (is_numeric($key)) {
                    $this->join .= ' ' . $value;
                }
            }
        } else if (is_string($table)) {
            $table = str_replace(':', self::$prefix, $table);
            $this->join .= " $link join $table " . (empty($on) ? '' : "on $on");
        }
        return $this;
    }

    /**
     * 插入数据
     * @param array $items
     * @return bool|int
     */
    public function insert(array $items) {
        if (!empty ($items)) {
            $param = [];
            $sql = 'insert into ' . $this->table . '(`' . implode('`,`', array_keys($items)) . '`) values(';
            foreach ($items as $value) {
                $sql .= '?,';
                $param[] = $value;
            }
            $sql = substr($sql, 0, strlen($sql) - 1);
            $sql .= ')';
            $result = self::$db->prepare($sql);
            if ($result->execute($param)) {
                return $result->rowCount();
            }
            return false;
        }
        return false;
    }

    /**
     * and
     * @return $this
     */
    public function _and() {
        $this->lastOper = 'and';
        return $this;
    }

    /**
     * or
     * @return $this
     */
    public function _or() {
        $this->lastOper = 'or';
        return $this;
    }

    /**
     * 查询记录
     * @return false|Record
     */
    public function select() {
        $sql = 'select ' . ($this->field ?: '*') . " from {$this->table} {$this->join} " . ($this->where ? 'where' : '');
        $sql .= $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return new \oreo\lib\db\Record($result);
        }
        return false;
    }

    public function count() {
        $tmpField = $this->field;
        $tmpLimit = $this->limit;
        $this->field = '';
        $count = $this->field('count(*)')->find()['count(*)'];
        $this->field = $tmpField;
        $this->limit = $tmpLimit;
        return $count;
    }

    /**
     * 数据更新
     * @param $set
     * @return bool|int
     */
    public function update($set) {
        $data = null;
        if (is_string($set)) {
            $data = $set;
        } else if (is_array($set)) {
            foreach ($set as $key => $value) {
                if (is_numeric($key)) {
                    $data .= ',' . $set[$key];
                } else {
                    $data .= ",`{$key}`=?";
                    $tmpParam[] = $value;
                }
            }
            $this->bindParam = array_merge($tmpParam, $this->bindParam);
            $data = substr($data, 1);
        }
        $sql = "update {$this->table} set $data where" . $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return $result->rowCount();
        }
        return false;
    }

    /**
     * 执行语句
     * @access public
     * @param  string $sql  sql指令
     * @return int
     */
    public function query($sql)
    {
        return self::$db->query($sql)->fetchAll();
    }

    /**
     * 删除数据
     * @return bool|int
     */
    public function delete() {
        $sql = "delete from {$this->table} where" . $this->dealParam();
        $result = self::$db->prepare($sql);
        if ($result->execute($this->bindParam)) {
            return $result->rowCount();
        }
        return false;
    }

    /**
     * 对where等进行处理
     * @return string
     */
    private function dealParam() {
        $sql = $this->where ?: '';
        $sql .= $this->order ?: '';
        $sql .= $this->limit ?: '';
        return $sql;
    }

    /**
     * 绑定参数
     * @param $key
     * @param string $value
     * @return $this
     */
    public function bind($key, $value = '') {
        if (is_array($key)) {
            $this->bindParam = array_merge($this->bindParam, $key);
        } else {
            $this->bindParam[$key] = $value;
        }
        return $this;
    }

    /**
     * 排序
     * @param $field
     * @param string $rule
     * @return $this
     */
    public function order($field, $rule = 'desc') {
        if ($this->order) {
            $this->order .= ",`$field` $rule";
        } else {
            $this->order = " order by `$field` $rule";
        }
        return $this;
    }

    /**
     * 分页
     * @param $start
     * @param int $count
     * @return $this
     */
    public function limit($start, $count = 0) {
        if ($count) {
            $this->limit = " limit $start,$count";
        } else {
            $this->limit = " limit $start";
        }
        return $this;
    }

    /**
     * 查询出单条数据
     * @return mixed
     */
    public function find() {
        return $this->limit('1')->select()->fetch();
    }

    /**
     * 查询出单条数据
     * @return mixed
     */
    public function all() {
        return $this->select()->fetchAll();
    }

    /**
     * 开始事务
     */
    public function begin() {
        self::$db->exec('begin');
    }

    /**
     * 提交事务
     */
    public function commit() {
        self::$db->exec('commit');
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        self::$db->exec('rollback');
    }

    public function field($field, $alias = '') {
        if (is_string($field)) {
            if (empty($alias)) {
                $this->field .= (empty($this->field) ? '' : ',') . $field . ' ';
            } else {
                $this->field .= (empty($this->field) ? '' : ',') . $field . ' as ' . $alias . ' ';
            }
        } else if (is_array($field)) {
            foreach ($field as $key => $value) {
                if (is_string($key)) {
                    $this->field .= (empty($this->field) ? '' : ',') . $key . ' as ' . $value . ' ';
                } else {
                    $this->field .= (empty($this->field) ? '' : ',') . $value . ' ';
                }
            }
        }
        return $this;
    }

    /**
     * 上一个插入id
     * @return int
     */
    public function lastInsertId() {
        return self::$db->lastInsertId();
    }

    public function __call($func, $arguments) {
        if (is_null(self::$db)) {
            return 0;
        }
        return call_user_func_array(array(
            self::$db,
            $func
        ), $arguments);
    }
}