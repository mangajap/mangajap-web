import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'repeat',
  pure: false,
})
export class RepeatPipe implements PipeTransform {
  transform(value): any {
    let res = [];
    for (let i = 1; i <= value; i++) {
      res.push(i);
    }
    return res;
  }
}