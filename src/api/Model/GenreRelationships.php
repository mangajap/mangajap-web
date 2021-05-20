<?php

use App\Database\Column;
use App\MVC\Model;

class GenreRelationships extends Model {

    public $id;
    public $genreId;
    public $mangaId;
    public $animeId;

    public function __construct($data = null) {
        parent::__construct($data);
    }

    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('genrerelationships');

        $this->setColumnMap([
            'id' => 'genrerelationships_id',
            'genreId' => 'genrerelationships_genreid',
            'mangaId' => 'genrerelationships_mangaid',
            'animeId' => 'genrerelationships_animeid',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'genreId',
            'mangaId',
            'animeId',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'genreId' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
        ]);

        $this->belongsTo(
            'genreId',
            Genre::class,
            'id',
            [
                'alias' => 'genre'
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

    public function getGenreId() {
        return $this->genreId;
    }

    public function setGenreId($genreId) {
        $this->genreId = $genreId;
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