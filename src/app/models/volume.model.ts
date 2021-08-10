import { JsonApiAttribute, JsonApiModelConfig, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import { Manga } from "./manga.model";

interface Titles {
  fr: string;
  en: string;
  en_jp: string;
  ja_jp: string;
}

@JsonApiModelConfig({
  endpoint: 'volumes'
})
@JsonApiType("volumes")
export class Volume extends JsonApiModel {
  @JsonApiAttribute() createdAt: string;
  @JsonApiAttribute() updatedAt: string;
  @JsonApiAttribute() titles: Titles = {
    fr: undefined,
    en: undefined,
    en_jp: undefined,
    ja_jp: undefined
  };
  @JsonApiAttribute() number: number;
  @JsonApiAttribute() startChapter: number;
  @JsonApiAttribute() endChapter: number;
  @JsonApiAttribute() published: string;
  @JsonApiAttribute() coverImage: string | null;

  @JsonApiRelationship() manga: Manga;
}
