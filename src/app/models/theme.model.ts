import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import { Anime } from "./anime.model";
import { Manga } from "./manga.model";

@JsonApiType("themes")
export class Theme extends JsonApiModel {

  @JsonApiAttribute()
  createdAt: string;

  @JsonApiAttribute()
  updatedAt: string;

  @JsonApiAttribute()
  title: string;

  @JsonApiAttribute()
  description: string;


  @JsonApiRelationship()
  manga: Manga[];

  @JsonApiRelationship()
  anime: Anime[];
}
