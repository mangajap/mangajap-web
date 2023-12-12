import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations"
import JsonApiModel from "../utils/json-api/json-api-model"
import Franchise from "./franchise"
import Genre from "./genre"
import Review from "./review"
import Season from './season'
import Staff from "./staff"
import Theme from "./theme"

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
    [language: string]: string;
  } = {};

  @JsonApiAttribute()
  slug?: string;

  @JsonApiAttribute()
  synopsis?: string;

  @JsonApiAttribute()
  startDate?: string;

  @JsonApiAttribute()
  endDate?: string | null;

  @JsonApiAttribute()
  origin?: string;

  @JsonApiAttribute()
  status?: string;

  @JsonApiAttribute()
  animeType?: string;

  @JsonApiAttribute()
  seasonCount?: number;

  @JsonApiAttribute()
  episodeCount?: number;

  @JsonApiAttribute()
  episodeLength?: number;

  @JsonApiAttribute()
  totalLength?: number;

  @JsonApiAttribute()
  averageRating?: number;

  @JsonApiAttribute()
  ratingRank?: number;

  @JsonApiAttribute()
  popularity?: number;

  @JsonApiAttribute()
  userCount?: number;

  @JsonApiAttribute()
  favoritesCount?: number;

  @JsonApiAttribute()
  reviewCount?: number;

  @JsonApiAttribute()
  coverImage?: string;

  @JsonApiAttribute()
  bannerImage?: string;

  @JsonApiAttribute()
  youtubeVideoId?: string


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