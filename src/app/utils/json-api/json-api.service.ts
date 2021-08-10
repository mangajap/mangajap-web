import { HttpClient, HttpErrorResponse, HttpHeaders, HttpParams, HttpResponse } from "@angular/common/http";
import { Observable, throwError } from "rxjs";
import { map, catchError } from "rxjs/operators";
import { environment } from "src/environments/environment";
import { JsonApiErrorConverter } from "./converter/json-api-error.converter";
import { JsonApiRequestConverter } from "./converter/json-api-request-converter";
import { JsonApiResponse, JsonApiResponseConverter } from "./converter/json-api-response-converter";
import JsonApiModel, { JsonApiModelMeta } from "./json-api-model";
import { JsonApiParams } from "./json-api-params";

interface JsonApiConfig {
  baseUrl: string,
  models: {
    [type: string]: ModelType<any>
  },
  headers?: HttpHeaders | {
    [header: string]: string | string[]
  },
}

export type ModelType<T extends JsonApiModel> = new () => T;

export class JsonApiService {

  private _config: JsonApiConfig;
  public get config(): JsonApiConfig {
    return this._config;
  }
  public set config(value: JsonApiConfig) {
    this._config = value;
    for (const type of Object.keys(value.models)) {
      const jsonApi = value.models[type].prototype.jsonApi || {};
      jsonApi.service = this;
    }

    JsonApiResponseConverter.models = value.models;
  }

  constructor(protected http: HttpClient) { }


  public findAll<T extends JsonApiModel>(
    modelType: ModelType<T>,
    params?: JsonApiParams,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T[]>> {
    const url = JsonApiService.buildUrl(modelType, this.config.baseUrl);
    const options = this.buildOptions(headers, params);

    return this.http.get(url, options).pipe(
      map((res: HttpResponse<object>) => this.handleResponse(res, modelType)),
      catchError((err: any) => this.handleErrorResponse(err))
    );
  }

  public find<T extends JsonApiModel>(
    modelType: ModelType<T>,
    id: string,
    params?: JsonApiParams,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const url = JsonApiService.buildUrl(modelType, this.config.baseUrl, id);
    const options = this.buildOptions(headers, params);

    return this.http.get(url, options).pipe(
      map((res: HttpResponse<any>) => this.handleResponse(res, modelType)),
      catchError((err: any) => this.handleErrorResponse(err))
    );
  }

  public create<T extends JsonApiModel>(
    model: T,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const modelType = model.constructor as ModelType<T>;
    const url = JsonApiService.buildUrl(modelType, this.config.baseUrl);
    const body = JsonApiRequestConverter.convertRequest(model);
    const options = this.buildOptions(headers);

    return this.http.post(url, body, options).pipe(
      map((res: HttpResponse<any>) => this.handleResponse(res, modelType)),
      catchError((err: any) => this.handleErrorResponse(err))
    );
  }

  public update<T extends JsonApiModel>(
    model: T,
    headers?: HttpHeaders | { [header: string]: string | string[] }
  ): Observable<JsonApiResponse<T>> {
    const modelType = model.constructor as ModelType<T>;
    const url = JsonApiService.buildUrl(modelType, this.config.baseUrl, model.id);
    const body = JsonApiRequestConverter.convertRequest(model);
    const options = this.buildOptions(headers);

    return this.http.patch(url, body, options).pipe(
      map((res: HttpResponse<any>) => this.handleResponse(res, modelType)),
      catchError((err: any) => this.handleErrorResponse(err))
    );
  }

  public delete<T extends JsonApiModel>(
    modelType: ModelType<T>,
    id: string,
    headers?: HttpHeaders | { [header: string]: string | string[] },
  ): Observable<Response> {
    const url = JsonApiService.buildUrl(modelType, this.config.baseUrl, id);
    const options = this.buildOptions(headers);

    return this.http.delete(url, options).pipe(
      catchError((err: any) => this.handleErrorResponse(err))
    )
  }



  private handleResponse<T extends JsonApiModel>(
    body: any,
    modelType: ModelType<T>
  ): JsonApiResponse<T> {
    if (!environment.production) {
      const requestSQL = body.split(/\r?\n/).slice(0, -1).join('\n')
      if (requestSQL !== "") {
        let sql = localStorage.getItem('request_sql') || '';
        sql += requestSQL + '\n';
        localStorage.setItem('request_sql', sql);
      }
    }
    body = body.split(/\r?\n/).pop();
    
    return JsonApiResponseConverter.convert(modelType, JSON.parse(body));
  }

  private handleErrorResponse(err: any): Observable<any> {
    if (err instanceof HttpErrorResponse) {
      const errors = JsonApiErrorConverter.convert(err.error)
      return throwError(errors);
    }

    return throwError(err);
  }





  private static buildUrl<T extends JsonApiModel>(modelType: ModelType<T>, baseUrl: string, id?: string): string {
    const jsonApi: JsonApiModelMeta = modelType.prototype.jsonApi;
    const endpoint = jsonApi.config?.endpoint || jsonApi.schema.type;

    return [baseUrl, endpoint, id].filter(x => x).join('/');
  }

  private buildOptions(headers?: HttpHeaders | { [header: string]: string | string[] }, params?: JsonApiParams) {
    return {
      headers: this.buildHeaders(headers),
      params: this.buildParams(params),
    }
  }

  private buildParams(params: JsonApiParams): HttpParams {
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

  private buildHeaders(headers: HttpHeaders | { [header: string]: string | string[] }): HttpHeaders {
    let httpHeaders: HttpHeaders = new HttpHeaders();

    if (this.config.headers instanceof HttpHeaders) {
      httpHeaders = this.config.headers;
    } else if (this.config.headers) {
      httpHeaders = new HttpHeaders(this.config.headers);
    }

    if (headers instanceof HttpHeaders) {
      for (const key of headers.keys()) {
        httpHeaders = httpHeaders.set(key, headers[key]);
      }
    } else if (headers) {
      for (const key in headers) {
        httpHeaders = httpHeaders.set(key, headers[key])
      }
    }

    return httpHeaders;
  }
}
