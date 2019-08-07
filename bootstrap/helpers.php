<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}


function ngrok_url($routeName, $paremeters = [])
{
    // 开发环境，并且配置了 NGROK_URL
    if (app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表是否绝对路径
        return $url . route($routeName, $paremeters, false);
    }
    return route($routeName, $paremeters);
}

function big_number($number, $scale = 2)
{
    return new \Moontoast\Math\BigNumber($number, $scale);
}
