import { Component } from '@angular/core';

import JsonApiService from './utils/json-api/json-api.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'mangajap';

  constructor(
    private jsonApiService: JsonApiService,
  ) { }

}
