<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class FunnelService
{

    private $capacity; // 容量
    private $leakingRate; // 泄露速率
    private $leftCapacity; // 剩余容量
    private $lastLeakedTime; // 上一次漏水时间

    public function __construct(float $capacity, float $leakingRate)
    {
        $this->capacity = $capacity;
        $this->leakingRate = $leakingRate;
        $this->leftCapacity = $capacity;
        $this->lastLeakedTime = time();
    }

    public function makeSpace()
    {
        $now = time();
//        距离上一次漏水过去了多久
        $deltaTime = $now - $this->lastLeakedTime;
//        计算已经腾出了多少空间
        $deltaSpace = $deltaTime * $this->leakingRate;

//        腾出空间最小单位是 1,太小就忽略
        if ($deltaSpace < 1) {
            return;
        }
//        增加剩余空间
        $this->leftCapacity += $deltaSpace;
//        记录漏水时间
        $this->lastLeakedTime = time();

//        如果剩余容量大于了容器容量,则剩余容量为容器容量
        $this->leftCapacity = ($this->leftCapacity > $this->capacity) ? $this->capacity : $this->leftCapacity;
    }

    public function watering(float $quota)
    {
        //漏水操作
        $this->makeSpace();
//        当还有空间时,则减少容器剩余空间
        if ($this->leftCapacity >= $quota) {
            $this->leftCapacity -= $quota;
//            dump($this->leftCapacity);
            return true;
        }
        return false;
    }
}
