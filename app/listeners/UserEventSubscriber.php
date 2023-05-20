<?php

namespace app\listeners;

class UserEventSubscriber
{
    /**
     * 处理用户登录事件
     */
    public function handleUserLogin($event) {
        echo '事件订阅1';
    }

    /**
     * 处理用户注销事件
     */
    public function handleUserLogout($event) {
        echo '事件订阅2';


    }

    /**
     * 为事件订阅者注册监听器
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        
        $events->listen(
            'app\events\BlogView',
            [UserEventSubscriber::class, 'handleUserLogin']
        );

        $events->listen(
            'app\events\BlogView',
            [UserEventSubscriber::class, 'handleUserLogout']
        );
    }
}
?>
