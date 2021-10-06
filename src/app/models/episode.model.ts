import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import { Anime } from "./anime.model";
import Season from './season.model';

interface Titles {
  fr: string;
  en: string;
  en_jp: string;
  ja_jp: string;
}

@JsonApiType("episodes")
export class Episode extends JsonApiModel {
  @JsonApiAttribute() createdAt: string;
  @JsonApiAttribute() updatedAt: string;
  @JsonApiAttribute() titles: Titles = {
    fr: undefined,
    en: undefined,
    en_jp: undefined,
    ja_jp: undefined
  };
  @JsonApiAttribute() seasonNumber: number;
  @JsonApiAttribute() relativeNumber: number;
  @JsonApiAttribute() number: number;
  @JsonApiAttribute() airDate: string | null;
  @JsonApiAttribute() episodeType: string

  @JsonApiRelationship() anime: Anime;
  @JsonApiRelationship() season: Season;

}
