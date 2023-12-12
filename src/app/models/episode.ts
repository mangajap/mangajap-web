import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Anime from "./anime";
import Season from './season';

enum EpisodeType {
  oav = "OAV"
}

@JsonApiType("episodes")
export default class Episode extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  titles: {
    [language: string]: string;
  } = {};

  @JsonApiAttribute()
  seasonNumber?: number;

  @JsonApiAttribute()
  relativeNumber?: number;

  @JsonApiAttribute()
  number?: number;

  @JsonApiAttribute()
  airDate?: string | null;

  @JsonApiAttribute()
  episodeType?: string;


  @JsonApiRelationship()
  anime?: Anime;

  @JsonApiRelationship()
  season?: Season;


  static readonly EpisodeType = EpisodeType;
}
