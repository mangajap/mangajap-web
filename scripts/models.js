class Resource {

    type;
    id;

    constructor(type) {
        this.type = type;
    }

    exists() {
        return this.id !== undefined
    }

    getIdentifier() {
        return {
            "type": this.type,
            "id": this.id
        }
    }

    fromJson(data) {}
    toJson() {}

    create() {
        const model = this;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", `/api/${this.type}`, false);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Authorization', 'Bearer da9233f9603faf286d5c48081e9a697d95f2b36cf11dcfd02204b0f22348a49f');
        xhr.send(this.toJson());
        try {
            console.log(xhr.responseText);
            const data = JSON.parse(xhr.responseText);
            model.fromJson(data.data);
        } catch (e) {
            throw 'JSON is not valid: ' + xhr.responseText;
        }
    }
}

class Manga extends Resource {

    canonicalTitle;
    title_fr;
    title_en;
    title_en_jp;
    title_ja_jp;
    synopsis;
    startDate;
    endDate;
    origin;
    status;
    mangaType;
    volumeCount;
    chapterCount;
    coverImage;
    bannerImage;

    volumes = [];
    genres = [];
    themes = [];
    staff = [];
    franchise = [];

    constructor() {
        super("manga");
    }

    fromJson(data) {
        this.id = data.id
    }

    toJson() {
        return JSON.stringify({
            "data": {
                "type": this.type,
                "id": this.id,
                "attributes": {
                    "canonicalTitle": this.canonicalTitle,
                    "titles": {
                        "fr": this.title_fr,
                        "en": this.title_en,
                        "en_jp": this.title_en_jp,
                        "ja_jp": this.title_ja_jp
                    },
                    "synopsis": this.synopsis,
                    "startDate": this.startDate,
                    "endDate": this.endDate,
                    "origin": this.origin,
                    "status": this.status,
                    "mangaType": this.mangaType,
                    "volumeCount": this.volumeCount,
                    "chapterCount": this.chapterCount,
                    'coverImage': this.coverImage,
                    'bannerImage': this.bannerImage,
                },
                'relationships': {
                    'genres': {
                        'data': this.genres
                            .filter(genre => genre.exists())
                            .map(genre => genre.getIdentifier())
                    },
                    'themes': {
                        'data': this.themes
                            .filter(theme => theme.exists())
                            .map(theme => theme.getIdentifier())
                    }
                }
            }
        });
    }

    create() {
        try {
            this.genres
                .filter(genre => !genre.exists() && genre.title_fr !== undefined)
                .forEach(genre => genre.create());
            this.themes
                .filter(theme => !theme.exists() && theme.title_fr !== undefined)
                .forEach(theme => theme.create());

            super.create();

            this.volumes
                .forEach(volume => volume.create());
            this.staff
                .filter(staff => staff.people !== undefined && staff.role !== undefined)
                .forEach(staff => staff.create());
            this.franchise
                .filter(franchise => franchise.destination !== undefined && franchise.role !== undefined)
                .forEach(franchise => franchise.create());
        } catch (e) {
            console.log(e);
        }
    }

}

class Anime extends Resource {

    canonicalTitle;
    title_fr;
    title_en;
    title_en_jp;
    title_ja_jp;
    startDate;
    endDate;
    origin;
    status;
    animeType;
    seasonCount;
    episodeCount;
    episodeLength;
    synopsis;
    coverImage;
    youtubeVideoId;

    episodes = [];
    genres = [];
    themes = [];
    staff = [];
    franchise = [];


    constructor() {
        super("anime");
    }

    fromJson(data) {
        this.id = data.id
    }

    toJson() {
        this.seasonCount = this.episodes
            .map(episode => episode.seasonNumber)
            .filter((value, index, self) => self.indexOf(value) === index)
            .length;
        this.episodeCount = this.episodes.length;

        return JSON.stringify({
            "data": {
                "type": this.type,
                "id": this.id,
                "attributes": {
                    "canonicalTitle": this.canonicalTitle,
                    "titles": {
                        "fr": this.title_fr,
                        "en": this.title_en,
                        "en_jp": this.title_en_jp,
                        "ja_jp": this.title_ja_jp
                    },
                    "synopsis": this.synopsis,
                    "startDate": this.startDate,
                    "endDate": this.endDate,
                    "origin": this.origin,
                    "status": this.status,
                    "animeType": this.animeType,
                    "seasonCount": this.seasonCount,
                    "episodeCount": this.episodeCount,
                    "episodeLength": this.episodeLength,
                    'coverImage': this.coverImage,
                    "youtubeVideoId": this.youtubeVideoId
                },
                'relationships': {
                    'genres': {
                        'data': this.genres
                            .filter(genre => genre.exists())
                            .map(genre => genre.getIdentifier())
                    },
                    'themes': {
                        'data': this.themes
                            .filter(theme => theme.exists())
                            .map(theme => theme.getIdentifier())
                    }
                }
            }
        });
    }

    create() {
        try {
            this.genres
                .filter(genre => !genre.exists() && genre.title_fr !== undefined)
                .forEach(genre => genre.create());
            this.themes
                .filter(theme => !theme.exists() && theme.title_fr !== undefined)
                .forEach(theme => theme.create());

            super.create();

            this.episodes
                .forEach(episode => episode.create());
            this.staff
                .filter(staff => staff.people !== undefined && staff.role !== undefined)
                .forEach(staff => staff.create());
            this.franchise
                .filter(franchise => franchise.destination !== undefined && franchise.role !== undefined)
                .forEach(franchise => franchise.create());
        } catch (e) {
            console.log(e);
        }
    }
}

class Genre extends Resource {

    title_fr;
    description;

    constructor() {
        super("genres");
    }

    toJson() {
        return JSON.stringify({
            "data": {
                "type": this.type,
                "id": this.id,
                "attributes": {
                    "titles": {
                        'fr': this.title_fr
                    },
                    "description": this.description
                }
            }
        });
    }
}

class Theme extends Resource {

    title_fr;
    description;

    constructor() {
        super("themes");
    }

    toJson() {
        return JSON.stringify({
            "data": {
                "type": this.type,
                "id": this.id,
                "attributes": {
                    "titles": {
                        'fr': this.title_fr
                    },
                    "description": this.description
                }
            }
        });
    }
}

class Volume extends Resource {

    title_fr;
    title_en;
    title_en_jp;
    title_ja_jp;
    number;
    startChapter;
    endChapter;
    published;
    coverImage;

    manga;

    constructor() {
        super("volumes");
    }

    toJson() {
        return JSON.stringify({
            'data': {
                'type': this.type,
                "id": this.id,
                'attributes': {
                    "titles": {
                        'fr': this.title_fr,
                        "en": this.title_en,
                        "en_jp": this.title_en_jp,
                        "ja_jp": this.title_ja_jp
                    },
                    "number": this.number,
                    "startChapter": this.startChapter,
                    "endChapter": this.endChapter,
                    "published": this.published,
                    "airDate": this.airDate,
                    "coverImage": this.coverImage,
                },
                "relationships": {
                    'manga': {
                        'data': this.manga.getIdentifier()
                    }
                },
            }
        });
    }

}

class Episode extends Resource {

    title_fr;
    title_en;
    title_en_jp;
    title_ja_jp;
    seasonNumber;
    relativeNumber;
    number;
    airDate;
    episodeType;

    anime;

    constructor() {
        super("episodes");
    }

    toJson() {
        return JSON.stringify({
            'data': {
                'type': this.type,
                "id": this.id,
                'attributes': {
                    "titles": {
                        'fr': this.title_fr,
                        "en": this.title_en,
                        "en_jp": this.title_en_jp,
                        "ja_jp": this.title_ja_jp
                    },
                    "seasonNumber": this.seasonNumber,
                    "relativeNumber": this.relativeNumber,
                    "number": this.number,
                    "airDate": this.airDate,
                    'episodeType': this.episodeType,
                },
                "relationships": {
                    'anime': {
                        'data': this.anime.getIdentifier()
                    }
                },
            }
        });
    }
}

class Staff extends Resource {

    role;

    people = new People();
    anime;
    manga;

    constructor() {
        super("staff");
    }

    toJson() {
        const json = {
            'data': {
                'type': this.type,
                "id": this.id,
                'attributes': {
                    "role": this.role,
                },
                'relationships': {
                    "people": {
                        "data": this.people.getIdentifier(),
                    },
                },
            },
        };

        if (this.manga !== undefined) {
            json.data.relationships.manga = {};
            json.data.relationships.manga.data = this.manga.getIdentifier();
        }
        else if (this.anime !== undefined) {
            json.data.relationships.anime = {};
            json.data.relationships.anime.data = this.anime.getIdentifier();
        }
        else {
            throw 'Staff does not have media related';
        }

        return JSON.stringify(json);
    }


    create() {
        if (!this.people.exists() && (this.people.firstName !== undefined || this.people.lastName !== undefined || this.people.pseudo !== undefined)) {
            this.people.create();
        }

        super.create();
    }
}

class People extends Resource {

    firstName;
    lastName;
    pseudo;

    constructor() {
        super("people");
    }

    fromJson(data) {
        this.id = data.id
    }

    toJson() {
        return JSON.stringify({
            'data': {
                'type': this.type,
                'id': this.id,
                'attributes': {
                    'firstName': this.firstName,
                    'lastName': this.lastName,
                    'pseudo': this.pseudo,
                },
            },
        });
    }
}

class Franchise extends Resource {

    role;

    source;
    destination;

    constructor() {
        super("franchises");
    }

    toJson() {
        return JSON.stringify({
            'data': {
                'type': this.type,
                "id": this.id,
                "attributes": {
                    "role": this.role
                },
                "relationships": {
                    "source": {
                        "data": this.source.getIdentifier()
                    },
                    "destination": {
                        "data": this.destination.getIdentifier()
                    }
                }
            }
        });
    }
}
