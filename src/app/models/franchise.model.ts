import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import { Anime } from "./anime.model";
import { Manga } from "./manga.model";

enum Role {
  adaptation = "adaptation",
  alternative_setting = "alternative_setting",
  alternative_version = "alternative_version",
  character = "character",
  full_story = "full_story",
  other = "other",
  parent_story = "parent_story",
  prequel = "prequel",
  sequel = "sequel",
  side_story = "side_story",
  spinoff = "spinoff",
  summary = "summary"
}

@JsonApiType("franchises")
export class Franchise extends JsonApiModel {
  @JsonApiAttribute() createdAt: string;
  @JsonApiAttribute() updatedAt: string;
  @JsonApiAttribute() role: string;

  @JsonApiRelationship() source?: Manga | Anime;
  @JsonApiRelationship() destination?: Manga | Anime;


  static readonly Role = Role;
}
