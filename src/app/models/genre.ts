import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Anime from "./anime";
import Manga from "./manga";

@JsonApiType("genres")
export default class Genre extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  title?: string;

  @JsonApiAttribute()
  description?: string;


  @JsonApiRelationship()
  manga?: Manga[];

  @JsonApiRelationship()
  anime?: Anime[];
}
