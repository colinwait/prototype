<?php

namespace App\Listeners;

use App\Events\WebhookEvent;
use App\Webhook\DingDingWebhook;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebhookListener
{
    private $prototype;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->prototype = config('prototype');
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     *
     * @return void
     */
    public function handle(WebhookEvent $event)
    {
        $webhook       = config('webhook.cat');
        $file_links    = $event->file_links;
        $type          = $event->type;
        $categories    = $this->prototype['categories'];
        $types         = $this->prototype['types'];
        $category_name = $categories[$event->category];
        $type_name     = $types[$type];
        $text          = '';
        if (!empty($file_links)) {
            $text .= "\n> " . $type_name . "：";
            foreach ($file_links as $file_link) {
                $text .= "\n> [{$file_link['name']}]({$file_link['url']})";
            }
        }
        $data      = [
            'title'   => "{$category_name} {$type_name}更新啦~",
            'text'    => "### {$category_name} {$type_name}更新啦~ \n " .
                "> ![screenshot](https://cdn.duitang.com/uploads/item/201501/02/20150102103220_smsCY.gif)\n" .
                "> 前方高能！产品汪又更新原型/文档了！赶紧看看TA给你挖了什么坑(•̀ω•́)✧ 如果找不到更新，清一下缓存~" . $text,
            'isAtAll' => true,
        ];
        $d_webhook = new DingDingWebhook();
        $d_webhook->send($webhook, 'markdown', $data);
    }
}
