import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations"
import JsonApiModel from "../utils/json-api/json-api-model"
import Franchise from "./franchise.model"
import Genre from "./genre.model"
import Review from "./review.model"
import Season from './season.model'
import Staff from "./staff.model"
import Theme from "./theme.model"

enum Status {
  airing = "En cours",
  finished = "Terminé",
  unreleased = "À sortir",
  upcoming = "À venir",
}

enum AnimeType {
  tv = "Série TV",
  movie = "Film",
  oav = "OAV"
}

@JsonApiType("anime")
export default class Anime extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  title?: string;

  @JsonApiAttribute()
  titles?: {
    fr: string;
    en: string;
    en_jp: string;
    ja_jp: string;
  } = {
      fr: undefined,
      en: undefined,
      en_jp: undefined,
      ja_jp: undefined
    };

  @JsonApiAttribute()
  slug?: string;

  @JsonApiAttribute()
  synopsis?: string;

  @JsonApiAttribute()
  startDate?: string;

  @JsonApiAttribute()
  endDate?: string | null;

  @JsonApiAttribute()
  get origin(): string | undefined {
    return this._origin?.toUpperCase();
  }
  set origin(value: string | undefined) {
    this._origin = value?.toUpperCase();
  }
  _origin?: string;

  @JsonApiAttribute()
  status: string;

  @JsonApiAttribute()
  animeType: string;

  @JsonApiAttribute()
  seasonCount: number;

  @JsonApiAttribute()
  episodeCount: number;

  @JsonApiAttribute()
  episodeLength: number;

  @JsonApiAttribute()
  totalLength: number;

  @JsonApiAttribute()
  averageRating: number;

  @JsonApiAttribute()
  ratingRank: number;

  @JsonApiAttribute()
  popularity: number;

  @JsonApiAttribute()
  userCount: number;

  @JsonApiAttribute()
  favoritesCount: number;

  @JsonApiAttribute()
  reviewCount: number;

  @JsonApiAttribute()
  coverImage: string;

  @JsonApiAttribute()
  bannerImage: string;

  @JsonApiAttribute()
  youtubeVideoId: string


  @JsonApiRelationship()
  seasons: Season[] = [];

  @JsonApiRelationship()
  genres: Genre[] = [];

  @JsonApiRelationship()
  themes: Theme[] = [];

  @JsonApiRelationship()
  staff: Staff[] = [];

  @JsonApiRelationship()
  reviews: Review[] = [];

  @JsonApiRelationship()
  franchises: Franchise[] = [];


  static readonly Status = Status;
  static readonly AnimeType = AnimeType;
}