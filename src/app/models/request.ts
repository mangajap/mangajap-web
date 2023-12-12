import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import User from "./user";

@JsonApiType("requests")
export default class Request extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  requestType?: string;

  @JsonApiAttribute()
  data?: string;

  @JsonApiAttribute()
  isDone?: boolean;

  @JsonApiAttribute()
  userHasRead?: boolean;


  @JsonApiRelationship()
  user?: User;
}
