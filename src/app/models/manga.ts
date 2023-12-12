import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Franchise from "./franchise";
import Genre from "./genre";
import Review from "./review";
import Staff from "./staff";
import Theme from "./theme";
import Volume from "./volume";

enum Origin {
  jp = "Japon",
  kr = "Corée du sud",
  fr = "Français",
  us = "Etats-Unis",
  cn = "Chine",
  hk = "Hong Kong",
}

enum Status {
  publishing = "En cours",
  finished = "Terminé",
  unreleased = "À sortir",
  upcoming = "À venir",
}

enum MangaType {
  bd = "BD",
  comics = "Comics",
  josei = "Josei",
  kodomo = "Kodomo",
  seijin = "Seijin",
  seinen = "Seinen",
  shojo = "Shōjo",
  shonen = "Shōnen",
  doujin = "Doujin",
  novel = "Novel",
  oneshot = "One shot",
  webtoon = "Webtoon",
}

@JsonApiType("manga")
export default class Manga extends JsonApiModel {

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
  endDate?: string;

  @JsonApiAttribute()
  origin?: string;

  @JsonApiAttribute()
  status?: string;

  @JsonApiAttribute()
  mangaType?: string;

  @JsonApiAttribute()
  volumeCount?: number;

  @JsonApiAttribute()
  chapterCount?: number;

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


  @JsonApiRelationship()
  volumes?: Volume[] = [];

  @JsonApiRelationship()
  genres?: Genre[] = [];

  @JsonApiRelationship()
  themes?: Theme[] = [];

  @JsonApiRelationship()
  staff?: Staff[] = [];

  @JsonApiRelationship()
  reviews?: Review[] = [];

  @JsonApiRelationship()
  franchises?: Franchise[] = [];



  static readonly Origin = Origin;
  static readonly Status = Status;
  static readonly MangaType = MangaType;
}
