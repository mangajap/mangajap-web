import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Manga from "./manga";

@JsonApiType("volumes")
export default class Volume extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  titles?: {
    [language: string]: string;
  } = {};

  @JsonApiAttribute()
  number?: number;

  @JsonApiAttribute()
  startChapter?: number;

  @JsonApiAttribute()
  endChapter?: number;

  @JsonApiAttribute()
  published?: string;

  @JsonApiAttribute()
  coverImage?: string | null;


  @JsonApiRelationship()
  manga?: Manga;
}
