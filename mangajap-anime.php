<?php

$anime = [
    'canonicalTitle' => "",
    'titles' => [
        'fr' => "",
        'en' => "",
        'en_jp' => "",
        'ja_jp' => "",
    ],
    'synopsis' => "",
    'startDate' => "", // yyyy-mm-dd
    'endDate' => "", // yyyy-mm-dd
    'origin' => "", // jp, kr, ...
    'status' => "", // airing, finished, planned
    'animeType' => "", // tv, movie, oav
    'seasonCount' => 0,
    'episodeCount' => 0,
    'episodeLength' => 0, // min
    'coverImage' => "", // file (type: image/*)
    'youtubeVideoId' => "",

    'episodes' => [
        [
            'number' => 0,
            'seasonNumber' => 0,
            'relativeNumber' => 0,
            'titles' => [
                'fr' => ""
            ],
            'airDate' => "",
            'episodeType' => "",
        ],
        // ...
    ],
    'genres' => [
        [
            'id' => 0,
        ],
        [
            'title' => "",
            'description' => "",
        ],
        // ...
    ],
    'themes' => [
        [
            'id' => 0,
        ],
        [
            'title' => "",
            'description' => "",
        ],
        // ...
    ],
    'staff' => [
        [
            'people' => [
                'id' => 0
            ],
            'role' => "", // story_and_art, author, illustrator, original_creator
        ],
        [
            'people' => [
                'firstName' => "",
                'lastName' => "",
                'pseudo' => "",
            ],
            'role' => "",
        ],
        // ...
    ],
    'franchise' => [
        [
            'destination' => [
                'type' => "",
                'id' => 0,
            ],
        ],
        // ...
    ],
];

$anime = [
    'data' => [
        'type' => 'anime',
        'attributes' => [
            'canonicalTitle' => "",
            'titles' => [
                'fr' => "",
                'en' => "",
                'en_jp' => "",
                'ja_jp' => "",
            ],
            'synopsis' => "",
            'startDate' => "", // yyyy-mm-dd
            'endDate' => "", // yyyy-mm-dd
            'origin' => "", // jp, kr, ...
            'status' => "", // airing, finished, planned
            'animeType' => "", // tv, movie, oav
            'seasonCount' => 0,
            'episodeCount' => 0,
            'episodeLength' => 0, // min
            'coverImage' => "", // file (type: image/*)
            'youtubeVideoId' => "",
        ],
        'relationships' => [
            'episodes' => [
                'data' => [
                    [
                        'type' => 'episodes',
                        'attributes' => [
                            'number' => 0,
                            'seasonNumber' => 0,
                            'relativeNumber' => 0,
                            'titles' => [
                                'fr' => ""
                            ],
                            'airDate' => "",
                            'episodeType' => "",
                        ]
                    ],
                    // ...
                ]
            ],
            'genres' => [
                'data' => [
                    [
                        'type' => 'genres',
                        'id' => 0
                    ],
                    [
                        'title' => "",
                        'description' => "",
                    ],
                    // ...
                ]
            ],
            'themes' => [
                [
                    'id' => 0,
                ],
                [
                    'title' => "",
                    'description' => "",
                ],
                // ...
            ],
            'staff' => [
                [
                    'people' => [
                        'id' => 0
                    ],
                    'role' => "", // story_and_art, author, illustrator, original_creator
                ],
                [
                    'people' => [
                        'firstName' => "",
                        'lastName' => "",
                        'pseudo' => "",
                    ],
                    'role' => "",
                ],
                // ...
            ],
            'franchise' => [
                [
                    'destination' => [
                        'type' => "",
                        'id' => 0,
                    ],
                ],
                // ...
            ],
        ]
    ],
];