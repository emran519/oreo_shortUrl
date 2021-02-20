<?php


namespace oreo\lib\db;


class record {
    private $result;

    public function __call($func, $arguments) {
        if (is_null($this->result)) {
            return 0;
        }
        return call_user_func_array(array(
            $this->result,
            $func
        ), $arguments);
    }

    function __construct(\PDOStatement $result) {
        $this->result = $result;
        $this->result->setFetchMode(\PDO::FETCH_ASSOC);
    }

    function fetchAll() {
       // return $this->result->debugDumpParams();
        return $this->result->fetchAll();
    }

    function fetch() {
        return $this->result->fetch();
    }

}