import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from 'src/environments/environment';
import { AnimeEntry } from '../models/anime-entry.model';
import { Anime } from '../models/anime.model';
import { Episode } from '../models/episode.model';
import { Follow } from '../models/follow.model';
import { Franchise } from '../models/franchise.model';
import { Genre } from '../models/genre.model';
import { MangaEntry } from '../models/manga-entry.model';
import { Manga } from '../models/manga.model';
import { People } from '../models/people.model';
import { Request } from '../models/request.model';
import { Review } from '../models/review.model';
import { Staff } from '../models/staff.model';
import { Theme } from '../models/theme.model';
import { User } from '../models/user.model';
import { Volume } from '../models/volume.model';
import { JsonApiService } from '../utils/json-api/json-api.service';
import { JsonApiParams } from '../utils/json-api/json-api-params';
import { OAuth2Body } from '../utils/oauth2/oauth2-body';
import { JsonApiResponse } from '../utils/json-api/converter/json-api-response-converter';

@Injectable({
  providedIn: 'root'
})
export class MangajapApiService extends JsonApiService {

  get apiToken(): string {
    return localStorage.getItem('token');
  }
  set apiToken(value: string) {
    localStorage.setItem('token', value);
    this.apiToken$.next(value);
  }
  apiToken$ = new BehaviorSubject(this.apiToken);

  selfUser: User = null;


  constructor(protected http: HttpClient) { 
    super(http);

    this.apiToken$.subscribe(token => {
      this.config = {
        baseUrl: environment.apiUrl,
        models: {
          anime: Anime,
          animeEntries: AnimeEntry,
          episodes: Episode,
          follows: Follow,
          franchises: Franchise,
          genres: Genre,
          manga: Manga,
          mangaEntries: MangaEntry,
          people: People,
          reviews: Review,
          staff: Staff,
          themes: Theme,
          users: User,
          volumes: Volume
        },
        headers: {
          Authorization: `Bearer ${token}`
        },
      }
    })
  }


  login(username: string, password: string): Observable<OAuth2Body> {
    return this.http.post<any>(`${environment.apiUrl}/oauth/token`, {
      grant_type: 'password',
      username: username,
      password: password
    }).pipe(
      map(response => {
        response = JSON.parse(response)
        if (response.access_token) {
          this.apiToken = response.access_token;
        } else {
          this.apiToken = '';
        }
        return response;
      })
    )
  }

  logout() {
    this.apiToken = '';
  }
}
