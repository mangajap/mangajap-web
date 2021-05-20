import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations"
import { JsonApiModel } from "../utils/json-api/json-api-model"
import { Episode } from "./episode.model"
import { Franchise } from "./franchise.model"
import { Genre } from "./genre.model"
import { Review } from "./review.model"
import { Staff } from "./staff.model"
import { Theme } from "./theme.model"

interface Titles {
  fr: string;
  en: string;
  en_jp: string;
  ja_jp: string;
}

enum Origin {
  jp = "Japon",
  kr = "Corée du sud",
  fr = "Français",
  us = "Etats-Unis",
  cn = "Chine",
  hk = "Hong Kong",
}

enum Status {
  airing = "En cours",
  finished = "Terminé",
  planning = "Pas encore commencé",
}

enum AnimeType {
  tv = "Série TV",
  movie = "Film",
  oav = "OAV"
}

@JsonApiType("anime")
export class Anime extends JsonApiModel {
  @JsonApiAttribute() createdAt: string;
  @JsonApiAttribute() updatedAt: string;
  @JsonApiAttribute() canonicalTitle: string;
  @JsonApiAttribute() titles: Titles = {
    fr: undefined,
    en: undefined,
    en_jp: undefined,
    ja_jp: undefined
  };
  @JsonApiAttribute() slug: string;
  @JsonApiAttribute() synopsis: string;
  @JsonApiAttribute() startDate: string;
  @JsonApiAttribute() endDate: string | null;
  @JsonApiAttribute() origin: string;
  @JsonApiAttribute() status: string;
  @JsonApiAttribute() animeType: string;
  @JsonApiAttribute() seasonCount: number;
  @JsonApiAttribute() episodeCount: number;
  @JsonApiAttribute() episodeLength: number;
  @JsonApiAttribute() totalLength: number;
  @JsonApiAttribute() averageRating: number;
  @JsonApiAttribute() ratingRank: number;
  @JsonApiAttribute() popularity: number;
  @JsonApiAttribute() userCount: number;
  @JsonApiAttribute() favoritesCount: number;
  @JsonApiAttribute() reviewCount: number;
  @JsonApiAttribute() coverImage: string;
  @JsonApiAttribute() youtubeVideoId: string

  @JsonApiRelationship() episodes: Episode[] = [];
  @JsonApiRelationship() genres: Genre[] = [];
  @JsonApiRelationship() themes: Theme[] = [];
  @JsonApiRelationship() staff: Staff[] = [];
  @JsonApiRelationship() reviews: Review[] = [];
  @JsonApiRelationship() franchise: Franchise[] = [];

  
  static readonly Origin = Origin;
  static readonly Status = Status;
  static readonly AnimeType = AnimeType;
}