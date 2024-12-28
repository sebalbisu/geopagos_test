<?php

return [
    'player_units' => [
        'skill' => [
            'min' => 0,
            'max' => 100,
            'handicap_weight' => 3,
            'handicap_inverse' => false,
        ],
        'strength' => [
            'min' => 0,
            'max' => 100,
            'handicap_weight' => 2,
            'handicap_inverse' => false,
        ],
        'speed' => [
            'min' => 0,
            'max' => 100,
            'handicap_weight' => 1,
            'handicap_inverse' => false,
        ],
        'latency' => [
            'min' => 0,
            'max' => 100,
            'handicap_weight' => 2,
            'handicap_inverse' => true,
        ],
    ],

];
