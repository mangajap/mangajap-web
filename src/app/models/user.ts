import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import AnimeEntry from "./anime-entry";
import Follow from "./follow";
import MangaEntry from "./manga-entry";
import Review from "./review";

@JsonApiType("users")
export default class User extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  uid?: string;

  @JsonApiAttribute()
  pseudo?: string;

  @JsonApiAttribute()
  slug?: string;

  @JsonApiAttribute()
  isAdmin?: boolean;

  @JsonApiAttribute()
  isPremium?: boolean;

  @JsonApiAttribute()
  about?: string;

  @JsonApiAttribute()
  followersCount?: number;

  @JsonApiAttribute()
  followingCount?: number;

  @JsonApiAttribute()
  followedMangaCount?: number;

  @JsonApiAttribute()
  volumesRead?: number;

  @JsonApiAttribute()
  chaptersRead?: number;

  @JsonApiAttribute()
  followedAnimeCount?: number;

  @JsonApiAttribute()
  episodesWatch?: number;

  @JsonApiAttribute()
  timeSpentOnAnime?: number;

  @JsonApiAttribute()
  firstName?: string;

  @JsonApiAttribute()
  lastName?: string;

  @JsonApiAttribute()
  birthday?: string;

  @JsonApiAttribute()
  gender?: string;

  @JsonApiAttribute()
  country?: string;

  @JsonApiAttribute()
  avatar?: {
    tiny: string;
    small: string;
    medium: string;
    large: string;
    original: string
  }

  @JsonApiAttribute()
  password?: string;


  @JsonApiRelationship()
  followers?: Follow[];

  @JsonApiRelationship()
  following?: Follow[];

  @JsonApiRelationship("manga-library")
  mangaLibrary?: MangaEntry[];

  @JsonApiRelationship("anime-library")
  animeLibrary?: AnimeEntry[];

  @JsonApiRelationship("manga-favorites")
  mangaFavorites?: MangaEntry[];

  @JsonApiRelationship("anime-favorites")
  animeFavorites?: AnimeEntry[];

  @JsonApiRelationship()
  reviews?: Review[];
}
