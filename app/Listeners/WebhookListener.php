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
        $webhook         = config('webhook.cat');
        $prototype_links = $event->prototype_links;
        $pdf_links       = $event->pdf_links;
        $categories      = $this->prototype['categories'];
        $category_name   = $categories[$event->category];
        $text            = '';
        if (!empty($prototype_links)) {
            $text .= "\n> 原型：";
            foreach ($prototype_links as $prototype_link) {
                $text .= "\n> [{$prototype_link['name']}]({$prototype_link['url']})";
            }
        }
        if (!empty($pdf_links)) {
            $text .= "\n> 文档：";
            foreach ($pdf_links as $pdf_link) {
                $text .= "\n> [{$pdf_link['name']}]({$pdf_link['url']})";
            }
        }
        $data      = [
            'title'   => "{$category_name} 原型/文档更新啦~",
            'text'    => "### {$category_name} 原型/文档更新啦~ \n " .
                "> ![screenshot](https://cdn.duitang.com/uploads/item/201501/02/20150102103220_smsCY.gif)\n" .
                "> 前方高能！产品汪又更新原型/文档了！赶紧看看TA给你挖了什么坑(•̀ω•́)✧ 如果找不到更新，清一下缓存~" . $text,
            'isAtAll' => true,
        ];
        $d_webhook = new DingDingWebhook();
        $d_webhook->send($webhook, 'markdown', $data);
    }
}
