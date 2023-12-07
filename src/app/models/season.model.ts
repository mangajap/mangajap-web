import { JsonApiType, JsonApiAttribute, JsonApiRelationship } from '../utils/json-api/json-api-annotations';
import JsonApiModel from '../utils/json-api/json-api-model';
import Anime from './anime.model';
import Episode from './episode.model';

@JsonApiType("seasons")
export default class Season extends JsonApiModel {

  @JsonApiAttribute()
  titles?: {
    [language: string]: string;
  } = {};
  
  @JsonApiAttribute()
  number?: number;
  
  @JsonApiAttribute()
  episodeCount?: number;

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;


  @JsonApiRelationship()
  anime?: Anime;

  @JsonApiRelationship()
  episodes?: Episode[] = [];
}
