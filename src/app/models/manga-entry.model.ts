import { JsonApiAttribute, JsonApiRelationship, JsonApiType } from "../utils/json-api/json-api-annotations";
import JsonApiModel from "../utils/json-api/json-api-model";
import Manga from "./manga.model";
import User from "./user.model";

@JsonApiType("mangaEntries", {
  endpoint: 'manga-entries'
})
export default class MangaEntry extends JsonApiModel {

  @JsonApiAttribute()
  createdAt: string;

  @JsonApiAttribute()
  updatedAt: string;

  @JsonApiAttribute()
  isAdd: boolean;

  @JsonApiAttribute()
  isFavorites: boolean;

  @JsonApiAttribute()
  status: string;

  @JsonApiAttribute()
  volumesRead: number;

  @JsonApiAttribute()
  chaptersRead: number;

  @JsonApiAttribute()
  startedAt: string;

  @JsonApiAttribute()
  finishedAt: string;

  @JsonApiAttribute()
  rating: number;

  @JsonApiAttribute()
  rereadCount: number;


  @JsonApiRelationship()
  user: User;

  @JsonApiRelationship()
  manga: Manga;
}
