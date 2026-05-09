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
        'AUTHOR' => [
            'value' => 2,
            'caption' => 'Author',
            'key' => 'AUTHOR'
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
    'book_categories' => [
        'ANTHOLOGY' => [
            'value' => 'ANTHOLOGY',
            'caption' => 'Anthology',
            'key' => 'ANTHOLOGY'
        ],
        'SINGLE_AUTHOR' => [
            'value' => 'SINGLE_AUTHOR',
            'caption' => 'Single Author',
            'key' => 'SINGLE_AUTHOR'
        ],
        'NATIVE' => [
            'value' => 'NATIVE',
            'caption' => 'Native',
            'key' => 'NATIVE'
        ]
    ],
    'pagination_limit' => 10,
    'RAZORPAY_KEY_ID' => 'rzp_test_zOAa3BnWo9nSva',
    'RAZORPAY_KEY_SECRET' => 'qf4XYy3e76E2AHFBZ8KyDYQl',
    'CREDENTIALS' => [
        'SMS' => [
            'URL' => "http://msg.icloudsms.com/rest/services/sendSMS/sendGroupSms?AUTH_KEY=",
            'SENDER_ID' => 'DNIRMA',
            'AUTHKEY' => '805b74daca86ea2e20ca9c166ee52f9e',
            'MESSAGES' => [
                'OTP' => "Dear User, Your OTP for login in Dainik Nirman App is {otp}."
            ]
        ]
    ]
]
?>