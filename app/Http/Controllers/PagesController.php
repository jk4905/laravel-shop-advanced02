<?php

namespace App\Http\Controllers;

use App\Services\FunnelService;
use App\Services\LfuService;
use App\Services\LruService;
use App\Services\TrafficShaperService;
use Illuminate\Support\Facades\Redis;

class PagesController extends Controller
{
    public function demo(){
        $client = new \Evernote\Client(array(
            'consumerKey' => '141759998',
            'consumerSecret' => 'fb40aa7bbaca5934'
        ));
        $requestToken = $client->getRequestToken('http://shop.kagami.top');
        $authorizeUrl = $client->getAuthorizeUrl($requestToken['oauth_token']);
        dd($authorizeUrl);
        exit;
    }

    public function lfuDemo()
    {
        $server = new LfuService(5);
        dump($server->set(1));
        dump($server->set(2));
        dump($server->set(3));
        dump($server->set(3));
        dump($server->set(4));
        dump($server->get(1));
        dump($server->get(1));
        dump($server->get(2));
        dump($server->get(2));
        dump($server->get(2));
        dump($server->get(3));
        dump($server->_capacity);
        dump($server->set(5));
        exit;
    }

    public function demo6()
    {
//        $redis = \Redis::connection('mycluster1');
//        $redis = new Redis();
        $redis = Redis::connection('mycluster1');
        $redis->del('codehole');
        $redis->xGroup('DESTROY', 'codehole', 'cg1');

        $redis->xAdd('codehole', '*', ['name' => 'laoqian', 'age' => '30']);
        $redis->xAdd('codehole', '*', ['name' => 'xiaoyu', 'age' => '29']);
        $redis->xAdd('codehole', '*', ['name' => 'xiaoqian', 'age' => '1']);

        $xrange = $redis->xrange('codehole', '-', '+');

        $xgroup1 = $redis->xGroup('Create', 'codehole', 'cg1', '0', true);

        $xlen = $redis->xLen('codehole');

        $xinfoGroups = $redis->xInfo('GROUPS', 'codehole');
        $xinfoStreams = $redis->xInfo('STREAM', 'codehole');
        dump($xgroup1);
        dump($xlen);
        dump($xrange);
        dump($xinfoGroups);
        dump($xinfoStreams);

        $xread1 = $redis->xReadGroup('cg1', 'c1', ['codehole' => '>'], 1, 1);
        $xread2 = $redis->xReadGroup('cg1', 'c1', ['codehole' => '>'], 1, 1);
        $xread3 = $redis->xReadGroup('cg1', 'c1', ['codehole' => '>'], 1, 1);
        dump($xread1);
        dump($xread2);
        dump($xread3);

        dump($redis->xInfo('GROUPS', 'codehole'));

        $xreadkey1 = array_keys($xread1['codehole'])[0];
        $xreadkey2 = array_keys($xread2['codehole'])[0];
        $xreadkey3 = array_keys($xread3['codehole'])[0];

        $xack1 = $redis->xAck('codehole', 'cg1', [$xreadkey1]);
        $xack2 = $redis->xAck('codehole', 'cg1', [$xreadkey2]);
        $xack3 = $redis->xAck('codehole', 'cg1', [$xreadkey3]);

        dump($xack1);
        dump($xack2);
        dump($xack3);
        dump($redis->xInfo('GROUPS', 'codehole'));

        exit;
        $redis->set('name', 'jack');
        $redis->set('sex', '女');
        $redis->set('age', '18');
        $redis->set('class', '一班');
        dump($redis->get('sex'));
        dump($redis->get('age'));
        dump($redis->get('class'));
        dd($redis->get('name'));
//        dd($redis->info());
    }

    public function demo4()
    {
        $redis = new \Predis\Client();
        $service = new TrafficShaperService($redis, 'TrafficShaper', 5);

        $service->reset();

        for ($i = 0; $i < 8; $i++) {
            dump($service->getToken());
        }

        dump($service->add(3));

        for ($i = 0; $i < 5; $i++) {
            dump($service->getToken());
        }
        return;
    }


    public function isActionAllowed3($userId, $action, $capacity, $leakingRate)
    {
        $key = sprintf("Funnel-%s:%s", $userId, $action);
        $funnel = $GLOBALS['funnel'][$key] ?? '';
        if (!$funnel) {
            $funnel = new FunnelService($capacity, $leakingRate);
            $GLOBALS['funnel'][$key] = $funnel;
        }
        return $funnel->watering(1);
    }


    public function demo3()
    {
        for ($i = 0; $i < 20; $i++) {
            dump($this->isActionAllowed("110", "reply", 15, 0.5)); //执行可以发现只有前15次是通过的
        }
    }

    /**
     * @param \Predis\Client $redis
     * @param String $userId (用户 id)
     * @param String $actionName (操作名)
     * @param Int $period 时间窗口(毫秒)
     * @param Int $maxCount (最大限制个数)
     * @return bool
     * @throws \Exception
     */
    public function isActionAllowed2(\Predis\Client $redis, String $userId, String $actionName, Int $period, Int $maxCount)
    {
//        设置键名
        $actionKey = sprintf('current-limiting.%s.%s', $actionName, $userId);
        list($msec, $sec) = explode(' ', microtime());
//        毫秒时间戳
        $now = intval(($sec + $msec) * 1000);

//        管道
        $replies = $redis->pipeline()
//        value 和 score 都用毫秒时间戳
            ->zadd($actionKey, $now, $now)
//        移除时间窗口之前的行为记录，剩下的都是时间窗口内的
            ->zremrangebyscore($actionKey, 0, $now - $period)
//        统计现在个数
            ->zcard($actionKey)
//        多加一秒过期时间
            ->expire($actionKey, $period + 1)
//        执行
            ->execute();
        return $replies[2] <= $maxCount;
    }


    public function demo1()
    {
        $redis = new \Predis\Client();

        $key = 'lock1';
        $requireId = 1;
        $ret = self::tryGetLock($redis, $key, $requireId, 1000);
        $ret2 = self::releaseLock($redis, $key, $requireId);
        dd($ret2);
    }

    const LOCK_SUCCESS = 'OK'; // 结果
    const IF_NOT_EXIST = 'NX'; // 如果不存在则创建
    const MILLISECONDS_EXPIRE_TIME = 'PX'; // 以毫秒为单位
    const RELEASE_SUCCESS = 1; // 释放成功

    /**
     * 尝试获取锁
     * @param \Predis\Client $redis redis客户端
     * @param String $key 锁
     * @param String $requestId 请求id
     * @param int $expireTime 过期时间
     * @return bool                     是否获取成功
     */
    public static function tryGetLock(\Predis\Client $redis, String $key, String $requireId, int $expireTime)
    {
        $result = $redis->set($key, $requireId, self::MILLISECONDS_EXPIRE_TIME, $expireTime, self::IF_NOT_EXIST);
        return (String)$result === self::LOCK_SUCCESS;
    }

    /**
     * @param \Predis\Client $redis redis客户端
     * @param String $key 锁
     * @param String $requireId 请求 id
     * @return bool
     */
    public static function releaseLock(\Predis\Client $redis, String $key, String $requireId)
    {
        // lua 脚本
        $lua = <<<'LUA'
        if redis.call('get', KEYS[1]) == ARGV[1] then 
            return redis.call('del', KEYS[1]) 
        else 
            return 0 
        end
LUA;
        $result = $redis->eval($lua, 1, $key, $requireId);
        return self::RELEASE_SUCCESS === $result;
    }
}
