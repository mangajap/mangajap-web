import { AnimeEntry } from "src/app/models/anime-entry.model";
import { Anime } from "src/app/models/anime.model";
import { Episode } from "src/app/models/episode.model";
import { Follow } from "src/app/models/follow.model";
import { Franchise } from "src/app/models/franchise.model";
import { Genre } from "src/app/models/genre.model";
import { MangaEntry } from "src/app/models/manga-entry.model";
import { Manga } from "src/app/models/manga.model";
import { People } from "src/app/models/people.model";
import { Review } from "src/app/models/review.model";
import { Staff } from "src/app/models/staff.model";
import { Theme } from "src/app/models/theme.model";
import { User } from "src/app/models/user.model";
import { Volume } from "src/app/models/volume.model";
import { JsonApiBody, JsonApiIdentifier, JsonApiResource } from "../json-api-body";
import { ModelType } from "../json-api.service";
import { JsonApiModel } from "../json-api-model";

export interface JsonApiResponse<T> {
  raw?: string,

  jsonapi?: {
    version: string;
  };
  data: T;
  included?: JsonApiResource[];
  meta?: any;
  links?: {
    first?: string;
    prev?: string;
    next?: string;
    last?: string;
  };
}

export class JsonApiResponseConverter {

  public static models?: { [type: string]: ModelType<any> };

  constructor() { }

  public static convert<T extends JsonApiModel>(modelType: ModelType<T>, body: JsonApiBody): JsonApiResponse<T> {
    let data = null;

    if (Array.isArray(body.data)) {
      data = body.data.map(data => this.fromJsonApi(data, body.included, modelType));
    } else if (body.data) {
      data = this.fromJsonApi(body.data, body.included, modelType);
    }

    return {
      raw: JSON.stringify(body),

      jsonapi: body.jsonapi,
      data: data,
      included: body.included,
      links: body.links,
      meta: body.meta,
    };
  }

  private static fromJsonApi<T extends JsonApiModel>(data: JsonApiResource, included: JsonApiResource[], modelType: ModelType<T>): T {
    const getRelation = (identifier: JsonApiIdentifier, included: JsonApiResource[]): JsonApiResource => {
      return included.find(resource => resource.type == identifier.type && resource.id == identifier.id)
    }

    const model: any = new modelType();
    model.initial = new modelType();

    // Id
    model[model.jsonApi?.mapping?.id || 'id'] = data.id;
    model.initial[model.jsonApi?.mapping?.id || 'id'] = data.id;

    // Attributes
    for (const [attribute, value] of Object.entries<any>(data.attributes)) {
      const property = model.jsonApi?.mapping?.attributes?.[attribute] || attribute;

      model[property] = value;

      if (typeof value === 'object' && value !== null) {
        model.initial[property] = Object.assign({}, value);
      } else {
        model.initial[property] = value;
      }
    }

    // Relationships
    for (const [relationship, value] of Object.entries<any>(data.relationships)) {
      const property = model.jsonApi?.mapping?.relationships?.[relationship] || relationship;

      if (!value.data) {
        model[property] = undefined;
        model.initial[property] = undefined;

      } else if (Array.isArray(value.data)) {
        model[property] = value.data.map(data => {
          return this.fromJsonApi(getRelation(data, included), included, this.models[data.type]);
        });
        model.initial[property] = value.data.map(data => {
          return this.fromJsonApi(getRelation(data, included), included, this.models[data.type]);
        });

      } else {
        model[property] = this.fromJsonApi(getRelation(value.data, included), included, this.models[value.data.type]);
        model.initial[property] = this.fromJsonApi(getRelation(value.data, included), included, this.models[value.data.type]);
      }
    }

    return model;
  }
}
