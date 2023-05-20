<?php
 //事件配置，listen可以自动发现

return [
    'listen' =>[ 'app\events\BlogView' => [
        'app\listeners\BlogViewListener',
        ],],


        'subscribe' => [ 'app\listeners\UserEventSubscriber',],


];
