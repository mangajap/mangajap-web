import JsonApiService from "./json-api.service";

export default interface JsonApiConfig {
  schema: {
    type: string;
    attributes: {
      [attribute: string]: string;
    };
    relationships: {
      [relationship: string]: string;
    };
  };
  service: JsonApiService;
  config: {
    endpoint?: string;
  };
}