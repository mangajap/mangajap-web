import JsonApi from "./json-api";
import JsonApiConfig from "./json-api-config";

export function JsonApiType(name: string, config?: JsonApiConfig['config']): any {
  return (constructor: Function): any => {
    JsonApi.models[name] = constructor as any;
    constructor.prototype.jsonApi = constructor.prototype.jsonApi || {};
    const jsonApi: JsonApiConfig = constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {} as any;
    jsonApi.schema.type = name;

    jsonApi.config = config || {};
  }
}

export function JsonApiAttribute(name?: string): any {
  return (target: any, property: string): any => {
    target.constructor.prototype.jsonApi = target.constructor.prototype.jsonApi || {};
    const jsonApi: JsonApiConfig = target.constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {} as any;
    jsonApi.schema.attributes = jsonApi.schema.attributes || {};
    jsonApi.schema.attributes[name || property] = property;
  };
}

export function JsonApiRelationship(name?: string): any {
  return (target: any, property: string): any => {
    target.constructor.prototype.jsonApi = target.constructor.prototype.jsonApi || {};
    const jsonApi: JsonApiConfig = target.constructor.prototype.jsonApi || {};

    jsonApi.schema = jsonApi.schema || {} as any;
    jsonApi.schema.relationships = jsonApi.schema.relationships || {};
    jsonApi.schema.relationships[name || property] = property;
  };
}
