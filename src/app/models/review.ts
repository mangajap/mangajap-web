import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Anime from "./anime";
import Manga from "./manga";
import User from "./user";

@JsonApiType("reviews")
export default class Review extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  content?: string


  @JsonApiRelationship()
  user?: User;

  @JsonApiRelationship()
  manga?: Manga;

  @JsonApiRelationship()
  anime?: Anime;
}
