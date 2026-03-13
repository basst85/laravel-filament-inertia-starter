<?php

return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'cachePath' => storage_path('app/purifier'),
    'settings' => [
        'default' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'p,b,strong,i,em,u,a[href|title|target],ul,ol,li,br,blockquote,h2,h3,h4,code,pre,span',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'Attr.AllowedFrameTargets' => ['_blank'],
        ],
    ],
];
