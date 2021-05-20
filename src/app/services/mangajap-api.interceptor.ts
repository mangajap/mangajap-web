import { HttpEvent, HttpHandler, HttpHeaders, HttpInterceptor, HttpParams, HttpRequest } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable()
export class MangaJapApiInterceptor implements HttpInterceptor {

  intercept(httpRequest: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (httpRequest.url.endsWith('/oauth/token')) {
      httpRequest = httpRequest.clone({
        body: this.queryParamsToString(httpRequest.body),
      });
    } else {
      httpRequest = httpRequest.clone({
        body: new HttpParams()
          .set('data', JSON.stringify(httpRequest.body))
          .set('REQUEST_METHOD', httpRequest.method)
          .set('Authorization', httpRequest.headers.get('Authorization'))
          .toString()
      });
    }

    httpRequest = httpRequest.clone({
      headers: new HttpHeaders({
        'Content-Type': 'application/x-www-form-urlencoded',
      }),
      method: 'POST'
    });

    return next.handle(httpRequest);
  }



  private queryParamsToString(params: any): string {
    const keyValuePairs = [];
    for (const key in params) {
      keyValuePairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
    }
    return keyValuePairs.join('&');
  }
}
