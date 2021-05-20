import { Pipe, PipeTransform } from "@angular/core";

@Pipe({
  name: 'enum',
  pure: false,
})
export class EnumPipe implements PipeTransform {
  transform(data: Object) {
    return Object.keys(data).map(key => {
      return { key: key, value: data[key]}
    });
  }
}