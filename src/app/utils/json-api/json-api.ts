import JsonApiBody, { JsonApiIdentifier, JsonApiResource } from "./json-api-body";
import JsonApiModel from "./json-api-model";

export interface ModelType<T extends JsonApiModel> {
  new(): T;
}

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

export default class JsonApi {

  public static models: {
    [type: string]: ModelType<any>
  } = {};


  public static decode<T extends JsonApiModel>(modelType: ModelType<T>, body: JsonApiBody): JsonApiResponse<T> {
    let data = null;

    if (Array.isArray(body.data)) {
      data = body.data.map(data => this.decodeModel(data, body.included, modelType));
    } else if (body.data) {
      data = this.decodeModel(body.data, body.included, modelType);
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

  private static decodeModel<T extends JsonApiModel>(data: JsonApiResource, included: JsonApiResource[], modelType: ModelType<T>): T {
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
          return this.decodeModel(getRelation(data, included), included, JsonApi.models[data.type]);
        });
        model.initial[property] = value.data.map(data => {
          return this.decodeModel(getRelation(data, included), included, JsonApi.models[data.type]);
        });

      } else {
        model[property] = this.decodeModel(getRelation(value.data, included), included, JsonApi.models[value.data.type]);
        model.initial[property] = this.decodeModel(getRelation(value.data, included), included, JsonApi.models[value.data.type]);
      }
    }

    return model;
  }


  public static encode(model: JsonApiModel): JsonApiBody {
    if (!model) throw new Error("model is invalid");

    const jsonApiSchema = model['jsonApi']?.schema;

    const data: JsonApiResource = {
      type: jsonApiSchema.type,
      id: model.id,
      attributes: {},
      relationships: {}
    };

    // Attributes
    for (const [attribute, property] of Object.entries<any>(jsonApiSchema.attributes)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (JSON.stringify(value) !== JSON.stringify(initValue)) {
        data.attributes[attribute] = value;
      }
    }

    // Relationships
    for (const [relationship, property] of Object.entries<any>(jsonApiSchema.relationships)) {
      const value = model[property];
      const initValue = model.initial?.[property];

      if (Array.isArray(value)) {
        const relationshipData = value
          .map(model => model.identifier())
          .filter(identifier => identifier.id)
          .filter(identifier => !initValue
            ?.map(initModel => initModel.identifier())
            ?.some(initIdentifier => (
              identifier.type === initIdentifier.type && identifier.id === initIdentifier.id
            )));

        if (relationshipData.length != 0) {
          data.relationships[relationship] = {
            data: relationshipData
          };
        }
      } else if (value) {
        const relationshipIdentifier = value.identifier();
        const relationshipInitIdentifier = initValue?.identifier();

        if (relationshipIdentifier.id) {
          if (JSON.stringify(relationshipIdentifier) !== JSON.stringify(relationshipInitIdentifier)) {
            data.relationships[relationship] = {
              data: relationshipIdentifier
            };
          }
        }
      }
    }

    return {
      data: data
    }
  }
}