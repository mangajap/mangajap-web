import { Observable } from "rxjs";
import { JsonApiResponse } from "./converter/json-api-response-converter";
import { JsonApiIdentifier } from "./json-api-body";
import { JsonApiParams } from "./json-api-params";
import { JsonApiService } from "./json-api.service";

export interface JsonApiModelMeta {
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

export default abstract class JsonApiModel {
  id?: string;

  initial: any;


  identifier(): JsonApiIdentifier {
    const jsonApi: JsonApiModelMeta = this.constructor.prototype.jsonApi;

    return {
      type: jsonApi.schema.type,
      id: this.id
    }
  }

  exists(): boolean {
    return !!this.id;
  }

  hasChanged(): boolean {
    return JSON.stringify({ ...this, initial: undefined }) !== JSON.stringify(this.initial);
  }

  
  public static findAll<T extends JsonApiModel>(this: new () => T, params?: JsonApiParams): Observable<JsonApiResponse<T[]>> {
    const jsonApi: JsonApiModelMeta = this.prototype.jsonApi;

    return jsonApi.service.findAll(this, params);
  }

  public static find<T extends JsonApiModel>(this: new () => T, id: string, params?: JsonApiParams): Observable<JsonApiResponse<T>> {
    const jsonApi: JsonApiModelMeta = this.prototype.jsonApi;

    return jsonApi.service.find(this, id, params);
  }

  public save(): Observable<JsonApiResponse<this>> {
    if (this.id) {
      return this.update();
    } else {
      return this.create();
    }
  }

  public create(): Observable<JsonApiResponse<this>> {
    const jsonApi: JsonApiModelMeta = this.constructor.prototype.jsonApi;

    // TODO: le code marche bien cependant il faut que lorsque par exemple on créer un manga, son id est attribué à ses relations (volumes,...) (volume.manga = response.data)
    // jsonApi.service.create(this).pipe(
    //   mergeMap(response => {
    //     const observables$: any[] = [];

    //     for (const [relationship, property] of Object.entries<any>(jsonApi.schema.relationships)) {
    //       const value = this[property];

    //       if (Array.isArray(value)) {
    //         observables$.push((value as JsonApiModel[])
    //           .filter(model => !model.id || model.hasChanged())
    //           .map(model => model.save()));

    //       } else if (value instanceof JsonApiModel) {
    //         if (!value.id || value.hasChanged()) {
    //           observables$.push(value.save());
    //         }
    //       }
    //     }

    //     return forkJoin(observables$)
    //   })
    // ).subscribe(response => console.log(response));
    // return;

    return jsonApi.service.create(this);
  }


  public update(): Observable<JsonApiResponse<this>> {
    const jsonApi: JsonApiModelMeta = this.constructor.prototype.jsonApi;

    return jsonApi.service.update(this);
  }
}
