<?php

namespace App\Services;

use Auth;

class LruService
{
    public $_capacity;
    public $_maxLen;

    public function __construct($maxLen = 5)
    {
        $this->_maxLen = $maxLen;
        $this->_capacity = [];
    }

    public function set($key)
    {
//        如果容器中有当前 key,则删除,并添加到容器头部
        if (in_array($key, $this->_capacity)) {
            $this->get($key);
        } elseif (count($this->_capacity) < $this->_maxLen) {
//        如果容器长度小于最大长度,则直接添加
            $this->add($key);
        } else {
//        否则,删除末尾元素,并将新元素放入头部
            array_shift($this->_capacity);
            $this->add($key);
        }
        return $this->_capacity;
    }

    public function get($key)
    {
        if (in_array($key, $this->_capacity)) {
            unset($this->_capacity[$key]);
            $this->add($key);
        }
        return $key;
    }

    public function add($key)
    {
        $this->_capacity[$key] = $key;
    }
}
