import { Pipe, PipeTransform } from '@angular/core';
import JsonApiConfig from './json-api-config';

@Pipe({
  name: 'jsonApiType'
})
export class JsonApiTypePipe implements PipeTransform {

  transform(value: any): any {
    const jsonApi: JsonApiConfig = value.constructor.prototype.jsonApi;

    return jsonApi.schema.type;
  }

}
