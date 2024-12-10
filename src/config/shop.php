<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/02/05 Fri 06:39 AM IRST
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

return [
    'providers' => [
        'payments' => [
            'gateways' => [
                [
                    'name' => 'parsian',
                    'title' => 'پارسیان',
                    'authenticate' => [
                        [
                            'label' => 'مرچنت‌کد',
                            'name' => 'mid'
                        ]
                    ],
                ],
                [
                    'name' => 'saman',
                    'title' => 'سامان',
                    'authenticate' => [
                        [
                            'label' => 'مرچنت‌کد',
                            'name' => 'mid'
                        ]
                    ],
                ],
                [
                    'name' => 'test',
                    'title' => 'تست',
                ]
            ]
        ],
        'shipping' => [
            'methods' => [
                [
                    'name' => 'static',
                    'title' => 'ثابت',
                ],
                [
                    'name' => 'alopeyk',
                    'title' => 'الوپیک',
                    'authenticate' => [
                        [
                            'label' => 'کلید دسترسی(Api Key)',
                            'name' => 'private_key'
                        ]
                    ]
                ],
            ]
        ]
    ]
];
