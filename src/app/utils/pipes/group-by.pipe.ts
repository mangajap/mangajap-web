import { Pipe, PipeTransform } from "@angular/core";
/*
 * Group an array by a property (key)
 * Usage:
 *  value | groupBy : 'field'
 */
@Pipe({
  name: 'groupBy',
  pure: false
})
export class GroupByPipe implements PipeTransform {
  transform(value: any[], field: string): any[] {
    if (!value) return [];

    const grouped = value.reduce((rv, x) => {
      (rv[x[field]] = rv[x[field]] || []).push(x);
      return rv;
    }, {});

    console.log(Object.keys(grouped).map(key => {
      return {
        key,
        value: grouped[key]
      };
    }));

    return Object.keys(grouped).map(key => ({
      key,
      value: grouped[key]
    }));
  }
}
