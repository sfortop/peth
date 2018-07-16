<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

return [
    'ethereum' => [
        'host' => parse_url(getenv('ETHEREUM_NODE_URL'))['host'] ?? null,
        'contracts' => [
            'DMT' => '0x2ccbff3a042c68716ed2a2cb0c544a9f1d1935e1',
        ],
    ],
];