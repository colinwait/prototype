<?php

namespace App\Webhook;

use GuzzleHttp\Client;

class DingDingWebhook
{
    function send($webhook, $msgType, $data)
    {
        if (is_null($webhook)) {
            return 'no robot';
        }

        $data = $this->getActionCard($msgType, $data);

        $client = new Client();
        try {
            $client->request('post', $webhook, ['json' => $data,'headers' => [
                'Content-Type' => 'application/json;charset=utf-8'
            ]]);
        } catch (\Exception $e) {

        }
    }

    private function getActionCard($msgType, $data)
    {
        $data = [
            "msgtype"    => $msgType,
            'actionCard' => [
                'title'          => isset($data['title']) ? $data['title'] : '',
                'text'           => isset($data['text']) ? $data['text'] : '',
                'hideAvatar'     => isset($data['hideAvatar']) && $data['hideAvatar'] ? 1 : 0,
                'btnOrientation' => isset($data['btnOrientation']) && $data['btnOrientation'] ? 1 : 0,
                'singleTitle'    => isset($data['singleTitle']) ? $data['singleTitle'] : '',
                'singleURL'      => isset($data['singleURL']) ? $data['singleURL'] : '',
            ],
            "text"       => [
                "content" => isset($data['text']) ? $data['text'] : '',
            ],
            "at"         => [
                "atMobiles" => isset($data['atMobiles']) && $data['atMobiles'] ? $data['atMobiles'] : [],
                "isAtAll"   => isset($data['isAtAll']) && $data['isAtAll'] ? true : false,
            ],
            "link"       => [
                "text"       => isset($data['text']) ? $data['text'] : '',
                "title"      => isset($data['title']) ? $data['title'] : '',
                "picUrl"     => isset($data['picUrl']) ? $data['picUrl'] : '',
                "messageUrl" => isset($data['messageUrl']) ? $data['messageUrl'] : '',
            ],
            "markdown"   => [
                "title" => isset($data['title']) ? $data['title'] : '',
                "text"  => isset($data['text']) ? $data['text'] : '',
            ]
        ];

        return $data;
    }
}