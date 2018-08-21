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
     * @return bool
     */
    public function handle(WebhookEvent $event)
    {
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
        if (!$config = $this->getWebhookConfig($event->category)) {
            return false;
        }

        if (isset($config[0])) {
            foreach ($config as $item) {
                $this->sendByConfig($item, $category_name, $type_name, $text);
            }
        } else {
            $this->sendByConfig($config, $category_name, $type_name, $text);
        }
    }

    private function sendByConfig($config, $category_name, $type_name, $text)
    {
        $default_data = [
            'title'   => "{$category_name} {$type_name}更新啦~",
            'text'    => "### {$category_name} {$type_name}更新啦~ \n " .
                "> ![screenshot]({{IMG}})\n" .
                "> {{TEXT}}" . $text,
            'isAtAll' => true,
        ];
        $default_img  = 'https://cdn.duitang.com/uploads/item/201501/02/20150102103220_smsCY.gif';
        $default_text = '前方高能！产品汪又更新原型/文档了！赶紧看看TA给你挖了什么坑(•̀ω•́)✧ 如果找不到更新，清一下缓存~';
        $webhook      = array_get($config, 'webhook', '');
        $data         = isset($config['data']) && $config['data'] ? $config['data'] : $default_data;
        $type         = array_get($config, 'type', 'markdown');
        $img          = isset($config['img']) && $config['img'] ? $config['img'] : $default_img;
        $context      = isset($config['text']) && $config['text'] ? $config['text'] : $default_text;
        if (!$webhook || !$data || !$type) {
            return false;
        }
        if (isset($data['text'])) {
            $data['text'] = strtr($data['text'], [
                '{{IMG}}'  => $img,
                '{{TEXT}}' => $context
            ]);
        }
        $data            = $data ?: [];
        $data['isAtAll'] = array_get($config, 'isAtAll', false);
        $d_webhook       = new DingDingWebhook();
//        dd($webhook, $type, $data);
        $d_webhook->send($webhook, $type, $data);
    }

    private function getWebhookConfig($category)
    {
        $configs = config('prototype.dd_webhook');

        return $configs[$category] ?? false;
    }
}
