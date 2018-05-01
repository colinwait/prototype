<?php
return [
    'categories' => [
        'mxu'     => 'MXU-1.2系列',
        'new-mxu' => 'MXU-1.3系列',
        'new-m2o' => 'M2O-Plus系列',
        'cloud'   => '云服务系列',
    ],
    'path'       => env('PROTOTYPE_PATH'),
    'host'       => env('PROTOTYPE_HOST'),
    'types'      => [
        'prototypes' => '原型文档',
        'docs'       => '普通文档',
        'designs'    => '设计稿（标注）',
    ],
    'file_types' => ['zip', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
];