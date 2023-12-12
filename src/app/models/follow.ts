import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import User from "./user";

@JsonApiType("follows")
export default class Follow extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;


  @JsonApiRelationship()
  follower?: User;

  @JsonApiRelationship()
  followed?: User;
}
