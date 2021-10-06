import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import { Franchise } from "./franchise.model";
import { Genre } from "./genre.model";
import { Review } from "./review.model";
import { Staff } from "./staff.model";
import { Theme } from "./theme.model";
import { Volume } from "./volume.model";

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
export class Manga extends JsonApiModel {

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
  franchise?: Franchise[] = [];



  static readonly Origin = Origin;
  static readonly Status = Status;
  static readonly MangaType = MangaType;
}
