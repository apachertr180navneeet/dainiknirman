<?php
return [
    'statuses' => [
        'ACTIVE' => [
            'value' => 1,
            'caption' => 'Active',
            'key' => 'ACTIVE',
        ],

        'INACTIVE' => [
            'value' => 0,
            'caption' => 'Inactive',
            'key' => 'INACTIVE',
        ],
    ],
    'roles' => [
        'ADMIN' => [
            'value' => 1,
            'caption' => 'Admin',
            'key' => 'ADMIN'
        ],
        'Author' => [
            'value' => 2,
            'caption' => 'User',
            'key' => 'USER'
        ],
        'READER' => [
            'value' => 3,
            'caption' => 'Reader',
            'key' => 'READER'
        ],
        'AUTHOR_READER' => [
            'value' => 4,
            'caption' => 'Author & Reader',
            'key' => 'AUTHOR_READER'
        ]
    ],
    'pagination_limit' => 10
]
?>