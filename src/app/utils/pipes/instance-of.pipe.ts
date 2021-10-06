import {Pipe, PipeTransform} from '@angular/core';

@Pipe({
  name: 'instanceof',
  pure: false,
})
export class InstanceOfPipe implements PipeTransform {

  transform(value: any): any {
    return value?.constructor.name;
  }

}