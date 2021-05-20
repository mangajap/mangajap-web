import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ 
  name: 'filter',
  pure: false,
})
export class FilterPipe implements PipeTransform {
  transform(items: any[], field: string, value: any): any[] {
    return items
      .filter(item => item[field] === value);
  }
}