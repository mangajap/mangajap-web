import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Staff from "./staff.model";

@JsonApiType("peoples")
export default class People extends JsonApiModel {

  @JsonApiAttribute()
  createdAt: string;

  @JsonApiAttribute()
  updatedAt: string;

  @JsonApiAttribute()
  firstName: string;

  @JsonApiAttribute()
  lastName: string;

  @JsonApiAttribute()
  pseudo: string;

  @JsonApiAttribute()
  image: string | null


  @JsonApiRelationship()
  staff: Staff[];

  @JsonApiRelationship()
  mangaStaff: Staff[];

  @JsonApiRelationship()
  animeStaff: Staff[];
}
