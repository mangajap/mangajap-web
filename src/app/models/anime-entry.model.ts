import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Anime from "./anime.model";
import User from "./user.model";

@JsonApiType("animeEntries", {
  endpoint: 'anime-entries'
})
export default class AnimeEntry extends JsonApiModel {

  @JsonApiAttribute()
  createdAt: string;

  @JsonApiAttribute()
  updatedAt: string;

  @JsonApiAttribute()
  isAdd: boolean;

  @JsonApiAttribute()
  isFavorites: boolean;

  @JsonApiAttribute()
  status: string;

  @JsonApiAttribute()
  episodesWatch: number;

  @JsonApiAttribute()
  startedAt: string;

  @JsonApiAttribute()
  finishedAt: string;

  @JsonApiAttribute()
  rating: number;

  @JsonApiAttribute()
  rewatchCount: number;


  @JsonApiRelationship()
  user: User;

  @JsonApiRelationship()
  anime: Anime;
}
