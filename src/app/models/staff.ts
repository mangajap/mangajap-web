import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Anime from "./anime";
import Manga from "./manga";
import People from "./people";

enum Role {
  author = "Scénariste",
  illustrator = "Dessinateur",
  story_and_art = "Créateur",
  licensor = "licensor",
  producer = "producer",
  studio = "studio",
  original_creator = "Créateur original"
}

@JsonApiType("staff")
export default class Staff extends JsonApiModel {

  @JsonApiAttribute()
  createdAt?: string;

  @JsonApiAttribute()
  updatedAt?: string;

  @JsonApiAttribute()
  role?: string


  @JsonApiRelationship()
  people?: People;

  @JsonApiRelationship()
  manga?: Manga;

  @JsonApiRelationship()
  anime?: Anime;



  static readonly Role = Role;
}
