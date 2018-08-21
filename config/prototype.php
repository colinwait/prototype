<?php
return [
    'categories' => [
//        'mxu'     => 'MXU-1.2系列',
//        'new-mxu' => 'MXU-1.3系列',
        'new-m2o' => 'M2O-Plus系列',
        'yindou'  => '音豆系列',
        'team'    => '协同系列',
        'fusion'  => '融合号系列',
        'factory' => '工厂系列',
        'cloud'   => '云服务系列',
    ],
    'path'       => env('PROTOTYPE_PATH'),
    'host'       => env('PROTOTYPE_HOST'),
    'types'      => [
        'prototypes' => '原型文档',
        'designs'    => '设计稿（标注）',
        'docs'       => '普通文档',
    ],
    'file_types' => ['zip', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
    'dd_webhook' => [
        'new-m2o' => [
            [
                'webhook' => 'https://oapi.dingtalk.com/robot/send?access_token=9558cb15bc6111b4bb4ff3d1ea5de9f1c41e6c0cdf9d35c48c643ea6d1adb73c',
                'type'    => 'markdown',
                'text'    => '前方高能！产品汪又更新原型/文档了！赶紧看看TA给你挖了什么坑(•̀ω•́)✧ 如果找不到更新，清一下缓存~',
                'img'     => 'https://cdn.duitang.com/uploads/item/201501/02/20150102103220_smsCY.gif',
                'isAtAll' => true
            ],
            [
                'webhook' => 'https://oapi.dingtalk.com/robot/send?access_token=3a36c8da643839265a4932c14911e3c1e54bbeb7c948cfd6e7be09639fb93023',
                'type'    => 'markdown',
                'text'    => '前方高能！产品汪又更新原型/文档了！赶紧看看TA给你挖了什么坑(•̀ω•́)✧ 如果找不到更新，清一下缓存~',
                'img'     => 'https://cdn.duitang.com/uploads/item/201501/02/20150102103220_smsCY.gif',
                'isAtAll' => false
            ],
        ]
    ]
];