import { JsonApiModelMeta } from "./json-api-model";

export function JsonApiModelConfig(config: JsonApiModelMeta['config']): any {
  return (constructor: Function): any => {
    constructor.prototype.jsonApi = constructor.prototype.jsonApi || {};
    const jsonApi = constructor.prototype.jsonApi || {};
    jsonApi.config = config;
  }
}

export function JsonApiType(name: string): any {
  return (constructor: Function): any => {
    constructor.prototype.jsonApi = constructor.prototype.jsonApi || {};
    const jsonApi = constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {};
    jsonApi.schema.type = name;
  }
}

export function JsonApiAttribute(name?: string): any {
  return (target: any, property: string): any => {
    target.constructor.prototype.jsonApi = target.constructor.prototype.jsonApi || {};
    const jsonApi = target.constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {};
    jsonApi.schema.attributes = jsonApi.schema.attributes || {};
    jsonApi.schema.attributes[name || property] = property;
  };
}

export function JsonApiRelationship(name?: string): any {
  return (target: any, property: string): any => {
    target.constructor.prototype.jsonApi = target.constructor.prototype.jsonApi || {};
    const jsonApi = target.constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {};
    jsonApi.schema.relationships = jsonApi.schema.relationships || {};
    jsonApi.schema.relationships[name || property] = property;
  };
}
