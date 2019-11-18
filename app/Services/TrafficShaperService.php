<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class TrafficShaperService
{
    public $_redis;
    public $_key;
    public $_maxCount;

    public function __construct(\Predis\Client $redis, String $key, Int $maxCount)
    {
        $this->_redis = $redis;
        $this->_key = $key;
        $this->_maxCount = $maxCount;
    }

    /**
     * 添加令牌
     * @param Int $num
     * @return Int
     */
    public function add(Int $num = 0)
    {
//        当前剩余令牌数
        $leftTokenCount = $this->_redis->llen($this->_key);

//        计算最大可加入的令牌数量，不能超过最大令牌数
        $num = ($leftTokenCount + $num > $this->_maxCount) ? 0 : $num;

        if ($num > 0) {
            $token = array_fill(0, $num, 1);
            $this->_redis->lpush($this->_key, $token);
            return $num;
        }
        return 0;
    }

    /**
     * 重置令牌桶
     */
    public function reset()
    {
        $this->_redis->del([$this->_key]);
        $this->add($this->_maxCount);
    }

    /**
     * 获取令牌
     * @return string
     */
    public function getToken()
    {
        return $this->_redis->rpop($this->_key);
    }

}
