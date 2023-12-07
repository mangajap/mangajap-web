import { HttpEvent, HttpHandler, HttpHeaders, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { from, Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable()
export class MangaJapApiInterceptor implements HttpInterceptor {

  constructor(
    private firebaseAuth: AngularFireAuth,
  ) { }

  intercept(httpRequest: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return from(this.handle(httpRequest, next));
  }

  private async handle(httpRequest: HttpRequest<any>, next: HttpHandler) {
    const firebaseUser = await this.firebaseAuth.currentUser;
    const idToken = await firebaseUser.getIdToken()

    httpRequest = httpRequest.clone({
      url: httpRequest.url.startsWith(environment.apiUrl) ? httpRequest.url : `${environment.apiUrl}/${httpRequest.url}`,
      headers: new HttpHeaders({
        Authorization: firebaseUser ? `Bearer ${idToken}` : '',
      }),
    });

    return next.handle(httpRequest).toPromise();
  }
}
