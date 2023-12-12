import { HttpClient, HttpErrorResponse, HttpHeaders, HttpParams, HttpResponse } from "@angular/common/http";
import { Injectable } from '@angular/core';
import { Observable, throwError } from "rxjs";
import { map, catchError } from "rxjs/operators";
import JsonApi, { JsonApiResponse, ModelType } from "./json-api";
import JsonApiConfig from "./json-api-config";
import JsonApiModel from "./json-api-model";

export interface JsonApiParams {
  include?: string | string[];
  fields?: {
    [type: string]: string | string[];
  };
  sort?: string | string[];
  limit?: number;
  offset?: number;
  filter?: {
    [type: string]: string | string[];
  };
}

@Injectable({
  providedIn: 'root'
})
export default class JsonApiService {

  constructor(private http: HttpClient) {
    for (const [type, model] of Object.entries(JsonApi.models)) {
      const jsonApi: JsonApiConfig = model.prototype.jsonApi || {};
      jsonApi.service = this;
    }
  }


  public findAll<T extends JsonApiModel>(
    modelType: ModelType<T>,
    params?: JsonApiParams,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T[]>> {
    const url = JsonApiService.buildUrl(modelType);
    const options = this.buildOptions(headers, params);

    return this.http.get(url, options).pipe(
      map((res) => this.handleResponse(res, modelType)),
      catchError((err) => this.handleErrorResponse(err))
    );
  }

  public find<T extends JsonApiModel>(
    modelType: ModelType<T>,
    id: string,
    params?: JsonApiParams,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const url = JsonApiService.buildUrl(modelType, id);
    const options = this.buildOptions(headers, params);

    return this.http.get(url, options).pipe(
      map((res) => this.handleResponse(res, modelType)),
      catchError((err) => this.handleErrorResponse(err))
    );
  }

  public create<T extends JsonApiModel>(
    model: T,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const modelType = model.constructor as ModelType<T>;
    const url = JsonApiService.buildUrl(modelType);
    const body = JsonApi.encode(model);
    const options = this.buildOptions(headers);

    return this.http.post(url, body, options).pipe(
      map((res) => this.handleResponse(res, modelType)),
      catchError((err) => this.handleErrorResponse(err))
    );
  }

  public update<T extends JsonApiModel>(
    model: T,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const modelType = model.constructor as ModelType<T>;
    const url = JsonApiService.buildUrl(modelType, model.id);
    const body = JsonApi.encode(model);
    const options = this.buildOptions(headers);

    return this.http.patch(url, body, options).pipe(
      map((res) => this.handleResponse(res, modelType)),
      catchError((err) => this.handleErrorResponse(err))
    );
  }

  public delete<T extends JsonApiModel>(
    modelType: ModelType<T>,
    id: string,
    headers?: HttpHeaders | { [header: string]: string | string[] },
  ): Observable<any> {
    const url = JsonApiService.buildUrl(modelType, id);
    const options = this.buildOptions(headers);

    return this.http.delete(url, options).pipe(
      catchError((err: any) => this.handleErrorResponse(err))
    )
  }



  private handleResponse<T extends JsonApiModel>(
    body: any,
    modelType: ModelType<T>
  ): JsonApiResponse<T> {
    return JsonApi.decode(modelType, body);
  }

  private handleErrorResponse(err: any): Observable<any> {
    if (err instanceof HttpErrorResponse) {
      return throwError(err.error);
    }

    return throwError(err);
  }





  private static buildUrl<T extends JsonApiModel>(modelType: ModelType<T>, id?: string): string {
    const jsonApi: JsonApiConfig = modelType.prototype.jsonApi;
    const endpoint = jsonApi.config?.endpoint || jsonApi.schema.type;

    return [endpoint, id].filter(x => x).join('/');
  }

  private buildOptions(headers?: HttpHeaders | { [header: string]: string | string[] }, params?: JsonApiParams) {
    return {
      params: this.buildParams(params),
    }
  }

  private buildParams(params?: JsonApiParams): HttpParams {
    let httpParams = new HttpParams();
    if (!params) return httpParams;

    if (params.include) {
      httpParams = httpParams.append("include", params.include.toString());
    }

    if (params.fields) {
      for (let type in params.fields) {
        httpParams = httpParams.append(`fields[${type}]`, params.fields[type].toString());
      }
    }

    if (params.sort) {
      httpParams = httpParams.append("sort", params.sort.toString());
    }

    if (params.limit) {
      httpParams = httpParams.append("page[limit]", params.limit.toString());
    }

    if (params.offset) {
      httpParams = httpParams.append("page[offset]", params.offset.toString());
    }

    if (params.filter) {
      for (let type in params.filter) {
        httpParams = httpParams.append(`filter[${type}]`, params.filter[type].toString());
      }
    }

    return httpParams;
  }
}
