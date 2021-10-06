import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ 
  name: 'filterNot',
  pure: false,
})
export class FilterNotPipe implements PipeTransform {
  transform(items: any[], field: string, value: any): any[] {
    return items
      .filter(item => item[field] != value);
  }
}