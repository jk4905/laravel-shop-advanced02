<?php

namespace App\Services;

use Auth;

class LfuService
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
//        如果容器中有当前 key,则值 +1
        if (in_array($key, array_keys($this->_capacity))) {
            $this->_capacity[$key]++;
        } elseif (count($this->_capacity) < $this->_maxLen) {
//        如果容器长度小于最大长度,则直接添加
            $this->_capacity[$key] = 1;
        } else {
//        否则排序,删除末尾元素,并将新元素放入头部
            array_pop($this->_capacity);
            $this->get($key);
        }
        return $this->_capacity;
    }

    public function get($key)
    {
        $this->_capacity[$key]++;
        arsort($this->_capacity);
        return $key;
    }
}
