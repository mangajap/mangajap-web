<?php

use App\Header;

function setUsers() {
    User::getInstance()->getWriteConnection()->execute("
            UPDATE
                user
            SET
                user_followerscount = (SELECT COUNT(*) FROM follow WHERE follow_followedid = user_id),

                user_followingcount = (SELECT COUNT(*) FROM follow WHERE follow_followerid = user_id),

                user_followedmangacount = (SELECT COUNT(*) FROM mangaentry WHERE mangaentry_userid = user_id AND mangaentry_isadd = 1),

                user_mangavolumesread = (SELECT COALESCE(SUM(mangaentry_volumesread * (mangaentry_rereadcount+1)), 0) FROM mangaentry WHERE mangaentry_userid = user_id),

                user_mangachaptersread = (SELECT COALESCE(SUM(mangaentry_chaptersread * (mangaentry_rereadcount+1)), 0) FROM mangaentry WHERE mangaentry_userid = user_id),

                user_followedanimecount = (SELECT COUNT(*) FROM animeentry WHERE animeentry_userid = user_id AND animeentry_isadd = 1),

                user_animeepisodeswatch = (SELECT COALESCE(SUM(animeentry_episodeswatch * (animeentry_rewatchcount+1)), 0) FROM animeentry WHERE animeentry_userid = user_id),

                user_animetimespent = (
                    SELECT
                        COALESCE(SUM(animeentry_episodeswatch * TIME_TO_SEC(anime_episodelength)), 0)
                    FROM
                        animeentry
                    RIGHT OUTER JOIN anime ON anime_id = animeentry_animeid
                    WHERE
                        animeentry_userid = user_id)"
    );
}

function setMangas() {
    Manga::getInstance()->getWriteConnection()->execute("
            UPDATE
                manga
            SET
                manga_rating = (
                    SELECT
                        AVG(mangaentry_rating)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_rating IS NOT NULL
                    GROUP BY
                        mangaentry_mangaid
                ),
                manga_usercount = (
                    SELECT
                        COUNT(*)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_isadd = 1
                ),
                manga_favoritescount = (
                    SELECT
                        COUNT(*)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_isfavorites = 1
                ),
                manga_reviewcount = (
                    SELECT
                        COUNT(*)
                    FROM
                        review
                    WHERE
                        review_mangaid = manga_id
                ),
                manga_popularity = (
                    SELECT
                        COALESCE(
                            (manga_usercount + manga_favoritescount) + 
                            manga_usercount * COALESCE(manga_rating, 0) + 
                            2 * COUNT(mangaentry_id) * COALESCE(manga_rating, 0) *(manga_usercount + manga_favoritescount),
                            0
                        )
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_updatedat BETWEEN(NOW() - INTERVAL 7 DAY) AND NOW()
                )"
    );

    $mangas = Manga::getInstance()->getWriteConnection()->query("
        SELECT
            *
        FROM
            manga;");
    foreach ($mangas as &$manga) {
        $rating = $manga['manga_rating'];
        $userCount = $manga['manga_usercount'];
        $favoritesCount = $manga['manga_favoritescount'];
        $manga['manga_weightedrank'] = ($userCount + $favoritesCount) + $rating * $userCount + 2 * $rating * $favoritesCount;
    }
    array_multisort(array_column($mangas, 'manga_weightedrank'), SORT_DESC, $mangas);
    for($i=0; $i<count($mangas); $i++) {
        $mangaId = $mangas[$i]["manga_id"];
        $mangaRank = $i + 1;

        Manga::getInstance()->getWriteConnection()->execute("
            UPDATE
                manga
            SET
                manga_ratingrank = :mangaRank
            WHERE
               manga_id = :mangaId;",
            [
                'mangaId' => $mangaId,
                'mangaRank' => $mangaRank
            ]);
    }
}

function setAnimes() {
    Anime::getInstance()->getWriteConnection()->execute("
        UPDATE
                anime
            SET
                anime_seasoncount =(
                    SELECT
                        MAX(episode_seasonnumber)
                    FROM
                        episode
                    WHERE
                        episode_animeid = anime_id
                ),
                anime_episodecount =(
                    SELECT
                        COUNT(*)
                    FROM
                        episode
                    WHERE
                        episode_animeid = anime_id
                ),
                anime_rating =(
                    SELECT
                        AVG(animeentry_rating)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_rating IS NOT NULL
                    GROUP BY
                        animeentry_animeid
                ),
                anime_usercount =(
                    SELECT
                        COUNT(*)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_isadd = 1
                ),
                anime_favoritescount =(
                    SELECT
                        COUNT(*)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_isfavorites = 1
                ),
                anime_popularity =(
                    SELECT
                        COALESCE(
                            (anime_usercount + anime_favoritescount) + 
                            anime_usercount * COALESCE(anime_rating, 0) + 
                            2 * COUNT(animeentry_id) * COALESCE(anime_rating, 0) *(anime_usercount + anime_favoritescount),
                            0
                        )
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_updatedat BETWEEN(NOW() - INTERVAL 7 DAY) AND NOW()
                )"
    );

    $animes = Anime::getInstance()->getWriteConnection()->query("
        SELECT
            *
        FROM
            anime;");
    foreach ($animes as &$anime) {
        $rating = $anime['anime_rating'];
        $userCount = $anime['anime_usercount'];
        $favoritesCount = $anime['anime_favoritescount'];
        $anime['anime_weightedrank'] = ($userCount + $favoritesCount) + $rating * $userCount + 2 * $rating * $favoritesCount;
    }
    array_multisort(array_column($animes, 'anime_weightedrank'), SORT_DESC, $animes);
    for($i=0; $i<count($animes); $i++) {
        $animeId = $animes[$i]["anime_id"];
        $animeRank = $i + 1;

        Anime::getInstance()->getWriteConnection()->execute("
            UPDATE
                anime
            SET
                anime_ratingrank = :animeRank
            WHERE
               anime_id = :animeId;",
            [
                'animeId' => $animeId,
                'animeRank' => $animeRank
            ]);
    }
}

$router->get(
    '/cronjobs',
    function() {
        setUsers();
        setMangas();
        setAnimes();
    }
);

$router->get(
    '/cronjobs/users',
    function() {
        Header::setAuthorization('Bearer 63ad406c1b40471014b10d3904ed4e824f4ca3acc63b83f76511b23fd360f2b7');

        $users = User::getList();

        foreach ($users as $user) {
            if (!$user instanceof User)
                continue;

            $user->setFollowersCount(Follow::count([
                'conditions' => "follow_followedid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setFollowingCount(Follow::count([
                'conditions' => "follow_followerid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setFollowedMangaCount(MangaEntry::count([
                'conditions' => "mangaentry_userid = :userId AND mangaentry_isadd = 1",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setVolumesRead((int) MangaEntry::sum([
                'columns' => "mangaentry_volumesread * (mangaentry_rereadcount+1)",
                'conditions' => "mangaentry_userid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setChaptersRead((int) MangaEntry::sum([
                'columns' => "mangaentry_chaptersread * (mangaentry_rereadcount+1)",
                'conditions' => "mangaentry_userid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setFollowedAnimeCount(AnimeEntry::count([
                'conditions' => "animeentry_userid = :userId AND animeentry_isadd = 1",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setEpisodesWatch((int) AnimeEntry::sum([
                'columns' => "animeentry_episodeswatch * (animeentry_rewatchcount+1)",
                'conditions' => "animeentry_userid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->setTimeSpentOnAnime((int) AnimeEntry::sum([
                'columns' => "animeentry_episodeswatch * TIME_TO_SEC(anime_episodelength)",
                'joins' => [
                    [
                        'type' => "RIGHT",
                        'model' => Anime::class,
                        'conditions' => "anime_id = animeentry_animeid",
                    ],
                ],
                'conditions' => "animeentry_userid = :userId",
                'bind' => [
                    'userId' => $user->getId(),
                ],
            ]));

            $user->update();
        }
    }
);

$router->get(
    '/cronjobs/manga',
    function() {
        Header::setAuthorization('Bearer 63ad406c1b40471014b10d3904ed4e824f4ca3acc63b83f76511b23fd360f2b7');

        $mangas = Manga::getList();

        foreach ($mangas as $manga) {
            if (!$manga instanceof Manga)
                continue;

            $manga->setAverageRating(MangaEntry::average([
                'columns' => "mangaentry_rating",
                'conditions' => "mangaentry_mangaid = :mangaId AND mangaentry_rating IS NOT NULL",
                'bind' => [
                    'mangaId' => $manga->getId(),
                ],
            ]));

            $manga->setUserCount(MangaEntry::count([
                'conditions' => "mangaentry_mangaid = :mangaId AND mangaentry_isadd = 1",
                'bind' => [
                    'mangaId' => $manga->getId(),
                ],
            ]));

            $manga->setFavoritesCount(MangaEntry::count([
                'conditions' => "mangaentry_mangaid = :mangaId AND mangaentry_isfavorites = 1",
                'bind' => [
                    'mangaId' => $manga->getId(),
                ],
            ]));

            $manga->setReviewCount(Review::count([
                'conditions' => "review_mangaid = :mangaId",
                'bind' => [
                    'mangaId' => $manga->getId(),
                ],
            ]));

            $manga->setPopularity(
                ($manga->userCount + $manga->favoritesCount) +
                $manga->userCount * $manga->averageRating +
                2 * ($manga->userCount + $manga->favoritesCount) * $manga->averageRating * MangaEntry::count([
                    'conditions' => [
                        "mangaentry_updatedat BETWEEN (NOW() - INTERVAL 7 DAY) AND NOW()",
                        "mangaentry_mangaid = :mangaId",
                    ],
                    'bind' => [
                        'mangaId' => $manga->getId(),
                    ],
                ])
            );

//            $manga->weightedRank = ($manga->getUserCount() + $manga->getFavoritesCount()) + $manga->getAverageRating() * $manga->getUserCount() + 2 * $manga->getAverageRating() * $manga->getFavoritesCount();

            $manga->update();
        }


//        $mangas = iterator_to_array($mangas);
//        usort($mangas, function($manga1, $manga2) {
//            if (!$manga1 instanceof Manga || !$manga2 instanceof Manga)
//                return 0;
//
//            if ($manga1->weightedRank == $manga2->weightedRank)
//                return 0;
//
//            return ($manga1->weightedRank > $manga2->weightedRank) ? +1 : -1;
//        });
//
//        $ratingRank = 0;
//        foreach ($mangas as $manga) {
//            if (!$manga instanceof Manga)
//                continue;
//
//            $manga->setRatingRank(++$ratingRank);
//        }
    }
);

$router->get(
    '/cronjobs/manga/volumes',
    function() {
        Header::setAuthorization('Bearer 63ad406c1b40471014b10d3904ed4e824f4ca3acc63b83f76511b23fd360f2b7');

        $mangaId = 33;
        $urls = [
            'https://fr.wikipedia.org/wiki/Liste_des_chapitres_de_Tokyo_Ghoul#Tokyo_Ghoul:re',
        ];

        foreach ($urls as $url) {
            $page = @file_get_contents($url);

            if ($page == null)
                return "erreur page";

            preg_match('@<h2><span class="mw-headline" id="Tokyo_Ghoul:re"><i>Tokyo Ghoul:re.*@s', $page, $match);
            $page = $match[0];
            preg_match_all('@<td style="border-top:solid 3px #[\w]*?; text-align:center; font-weight:bold;">([0-9]+)(?:.*?<time class="nowrap date-lien" datetime="([\d]{4}-[\d]{2}-[\d]{2}))(?:.*?chapitres&#160;:</b><br /><div>(?:\s<ul><li>Chapitre ([\d]+).*?<li>Chapitre ([\d]+)[^\n\r]*</li>(?:\s<li>(?:Hors).*?)?</ul>))@s', $page, $matches);
//            preg_match_all('@<td style="border-top:solid 3px #[\w]*?; text-align:center; font-weight:bold;">([0-9]+).*?(?:<time class="nowrap date-lien" datetime="([\d]{4}-[\d]{2}-[\d]{2}).*?)(?:<td colspan="5" style="vertical-align:top; padding:5px;">(?:<b>Titre du volume&#160;:</b><br />(?:(?:<i>)?(.*?)?(?: <span|</i>).*?)?(?:.*?lang="ja">(.*?)(?:</rb><rp>.*?</rp></ruby>)?</span>.*?(?:lang="ja-latn-alalc97">(.*?)</span>.*?)))?)?chapitres&#160;:</b><br /><div>(?:\s<ul><li>Bub ([\d]+).*?<li>Bub ([\d]+)[^\n\r]*</li>(?:\s<li>(?:Bub Hors).*?)?</ul>)?@s', $page, $matches);

//            print_r($matches);
            if (empty($matches[0]))
                return "erreur regex";

            for ($i=0; $i<count($matches[0]); $i++) {
                $title_fr = trim(str_replace('&#160;', ' ',  ""));
                $title_en = trim(str_replace('&#160;', ' ', ""));
                $title_en_jp = trim(str_replace('&#160;', ' ', ""));
                $title_ja_jp = trim(str_replace('&#160;', ' ', ""));
                $number = (int) $matches[1][$i];
                $startChapter = $matches[3][$i];
                $endChapter = $matches[4][$i];
                $published = $matches[2][$i];

                $startChapter = $startChapter === "" ? null : (int) $startChapter;
                $endChapter = $endChapter === "" ? null : (int) $endChapter;
                $published = $published === "" ? null : $published;
//                if (isset($published)) {
//                    $published = str_replace(
////                        ['janvier', 'fvrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aot', 'septembre', 'octobre', 'novembre', 'dcembre'],
//                        ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
//                        ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
//                        \App\Utils\Slug::generate($published)
//                    );
//                    $published = preg_match('@^([\d]{2})-([\d]{2})-([\d]{4})$@', $published) ?
//                        preg_replace('@([\d]+)-([\d]+)-([\d]+)@', '$3-$1-$2', $published) :
//                        preg_replace('@([\d]+)-([\d]+)-([\d]+)@', '$3-$1-0$2', $published);
////                    var_dump($published);
//                }


                $volume = new Volume();

                $volume->setTitleFr($title_fr);
                $volume->setTitleEn($title_en);
                $volume->setTitleEnJp($title_en_jp);
                $volume->setTitleJaJp($title_ja_jp);
                $volume->setNumber($number);
                $volume->setStartChapter($startChapter);
                $volume->setEndChapter($endChapter);
                $volume->setPublished($published);
                $volume->setMangaId($mangaId);

//                print_r($volume);

//                if (!$volume->create())
//                    return "erreur volume: " . $volume->number;
            }
        }

        return Manga::get($mangaId)->getRelated("volumes");
    }
);

$router->get(
    '/cronjobs/manga/chapters',
    function() {
        Header::setAuthorization('Bearer 63ad406c1b40471014b10d3904ed4e824f4ca3acc63b83f76511b23fd360f2b7');

        $mangas = Manga::getList();

        foreach ($mangas as $manga) {
            if (!$manga instanceof Manga)
                continue;

            $page = @file_get_contents("https://www.japscan.se/manga/" . $manga->slug . "/");

            if($page != null) {

                preg_match('@<a class="text-dark"\shref=".*?([0-9]+) VF.*?</a>@is', $page, $chapters);

                if (array_key_exists(1, $chapters)) {
//                    $volumeCount = $manga->volumeCount;
                    $chapterCount = $chapters[1];

                    if ($chapterCount > $manga->chapterCount) {

//                        $manga->setVolumeCount($volumeCount);
                        $manga->setChapterCount($chapterCount);

                        $manga->update();
                    }
                }
            }
        }
    }
);

$router->get(
    '/cronjobs/anime',
    function() {
        Header::setAuthorization('Bearer 63ad406c1b40471014b10d3904ed4e824f4ca3acc63b83f76511b23fd360f2b7');

        $animes = Anime::getList();

        foreach ($animes as $anime) {
            if (!$anime instanceof Anime)
                continue;

            $anime->setSeasonCount((int) Episode::maximum([
                'columns' => "episode_seasonnumber",
                'conditions' => "episode_animeid = :animeId",
                'bind' => [
                    'animeId' => $anime->getId(),
                ],
            ]));

            $anime->setEpisodeCount(Episode::count([
                'conditions' => "episode_animeid = :animeId",
                'bind' => [
                    'animeId' => $anime->getId(),
                ],
            ]));

            $anime->setAverageRating(AnimeEntry::average([
                'columns' => "animeentry_rating",
                'conditions' => "animeentry_animeid = :animeId AND animeentry_rating IS NOT NULL",
                'bind' => [
                    'animeId' => $anime->getId(),
                ],
            ]));

            $anime->setUserCount(AnimeEntry::count([
                'conditions' => "animeentry_animeid = :animeId AND animeentry_isadd = 1",
                'bind' => [
                    'animeId' => $anime->getId(),
                ],
            ]));

            $anime->setFavoritesCount(AnimeEntry::count([
                'conditions' => "animeentry_animeid = :animeId AND animeentry_isfavorites = 1",
                'bind' => [
                    'animeId' => $anime->getId(),
                ],
            ]));

            $anime->setPopularity(
                ($anime->getUserCount() + $anime->getFavoritesCount()) +
                $anime->getUserCount() * $anime->getAverageRating() +
                2 * ($anime->getUserCount() + $anime->getFavoritesCount()) * $anime->getAverageRating() * AnimeEntry::count([
                    'conditions' => [
                        "animeentry_updatedat BETWEEN (NOW() - INTERVAL 7 DAY) AND NOW()",
                        "animeentry_animeid = :animeId",
                    ],
                    'bind' => [
                        'animeId' => $anime->getId(),
                    ],
                ])
            );

//            $anime->weightedRank = ($anime->getUserCount() + $anime->getFavoritesCount()) + $anime->getAverageRating() * $anime->getUserCount() + 2 * $anime->getAverageRating() * $anime->getFavoritesCount();

            $anime->update();
        }

//        $animes = iterator_to_array($animes);
//        usort($animes, function($anime1, $anime2) {
//            if (!$anime1 instanceof Anime || !$anime2 instanceof Anime)
//                return 0;
//
//            if ($anime1->weightedRank == $anime2->weightedRank)
//                return 0;
//
//            return ($anime1->weightedRank > $anime2->weightedRank) ? +1 : -1;
//        });
//
//        $ratingRank = 0;
//        foreach ($animes as $anime) {
//            if (!$anime instanceof Anime)
//                continue;
//
//            $anime->setRatingRank(++$ratingRank);
//        }
    }
);