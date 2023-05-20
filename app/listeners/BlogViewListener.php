<?php
namespace app\listeners;

use app\events\BlogView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BlogViewListener
{
public function __construct()
{
}

    /**
     * Handle the event.
     *
     * @param  BlogView  $event
     * @return void
     */
    public function handle(BlogView $event)
    {
        echo '执行事件。。。';
    }
}
 