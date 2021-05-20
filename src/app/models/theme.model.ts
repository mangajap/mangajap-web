import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import { JsonApiModel } from "../utils/json-api/json-api-model";
import { Anime } from "./anime.model";
import { Manga } from "./manga.model";

interface Titles {
  fr: string;
}

@JsonApiType("themes")
export class Theme extends JsonApiModel {
  @JsonApiAttribute() createdAt: string;
  @JsonApiAttribute() updatedAt: string;
  @JsonApiAttribute() title: string;
  @JsonApiAttribute() titles: Titles = {
    fr: undefined
  };
  @JsonApiAttribute() description: string;

  @JsonApiRelationship() manga: Manga[];
  @JsonApiRelationship() anime: Anime[];
}
