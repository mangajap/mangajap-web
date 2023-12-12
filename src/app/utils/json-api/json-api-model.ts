import { firstValueFrom } from "rxjs";
import { JsonApiResponse } from "./json-api";
import { JsonApiIdentifier } from "./json-api-body";
import JsonApiConfig from "./json-api-config";
import { JsonApiParams } from "./json-api.service";

export default abstract class JsonApiModel {

  type?: string;
  id?: string;

  initial: any;


  identifier(): JsonApiIdentifier {
    const jsonApi: JsonApiConfig = this.constructor.prototype.jsonApi;

    return {
      type: jsonApi.schema.type,
      id: this.id!,
    }
  }

  exists(): boolean {
    return !!this.id;
  }

  hasChanged(): boolean {
    return JSON.stringify({ ...this, initial: undefined }) !== JSON.stringify(this.initial);
  }


  public static findAll<T extends JsonApiModel>(this: new () => T, params?: JsonApiParams): Promise<JsonApiResponse<T[]>> {
    const jsonApi: JsonApiConfig = this.prototype.jsonApi;

    return firstValueFrom(jsonApi.service.findAll(this, params));
  }

  public static find<T extends JsonApiModel>(this: new () => T, id: string, params?: JsonApiParams): Promise<JsonApiResponse<T>> {
    const jsonApi: JsonApiConfig = this.prototype.jsonApi;

    return firstValueFrom(jsonApi.service.find(this, id, params));
  }

  
  public save(): Promise<JsonApiResponse<this>> {
    if (this.id) {
      return this.update();
    } else {
      return this.create();
    }
  }

  public create(): Promise<JsonApiResponse<this>> {
    const jsonApi: JsonApiConfig = this.constructor.prototype.jsonApi;

    return firstValueFrom(jsonApi.service.create(this));
  }


  public update(): Promise<JsonApiResponse<this>> {
    const jsonApi: JsonApiConfig = this.constructor.prototype.jsonApi;

    return firstValueFrom(jsonApi.service.update(this));
  }
}
