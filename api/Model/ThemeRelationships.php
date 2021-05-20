<?php

use App\Database\Column;
use App\MVC\Model;

class ThemeRelationships extends Model {

    public $id;
    public $themeId;
    public $mangaId;
    public $animeId;

    public function __construct($data = null) {
        parent::__construct($data);
    }

    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('themerelationships');

        $this->setColumnMap([
            'id' => 'themerelationships_id',
            'themeId' => 'themerelationships_themeid',
            'mangaId' => 'themerelationships_mangaid',
            'animeId' => 'themerelationships_animeid',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'themeId',
            'mangaId',
            'animeId',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'themeId' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
        ]);


        $this->belongsTo(
            'themeId',
            Theme::class,
            'id',
            [
                'alias' => 'theme'
            ]
        );

        $this->belongsTo(
            'mangaId',
            Manga::class,
            'id',
            [
                'alias' => 'manga'
            ]
        );

        $this->belongsTo(
            'animeId',
            Anime::class,
            'id',
            [
                'alias' => 'anime'
            ]
        );
    }

    public function beforeSave(): bool {
        $user = User::fromAccessToken();

        if (!$user instanceof User)
            return false;

        if (!$user->isAdmin())
            return false;

        return true;
    }



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getThemeId() {
        return $this->themeId;
    }

    public function setThemeId($themeId) {
        $this->themeId = $themeId;
    }

    public function getMangaId() {
        return $this->mangaId;
    }

    public function setMangaId($mangaId) {
        $this->mangaId = $mangaId;
    }

    public function getAnimeId() {
        return $this->animeId;
    }

    public function setAnimeId($animeId) {
        $this->animeId = $animeId;
    }
}