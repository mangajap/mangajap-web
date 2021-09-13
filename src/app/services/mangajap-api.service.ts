import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { User } from '../models/user.model';
import JsonApiService from '../utils/json-api/json-api.service';

@Injectable({
  providedIn: 'root'
})
export class MangajapApiService extends JsonApiService {

  selfUser: User = null;


  constructor(
    protected http: HttpClient,
  ) {
    super(http);
  }
}
