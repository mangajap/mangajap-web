import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpHeaders
} from '@angular/common/http';
import { Auth } from '@angular/fire/auth';
import { Observable, from, lastValueFrom } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable()
export class MangajapApiInterceptor implements HttpInterceptor {

  constructor(
    private firebaseAuth: Auth,
  ) { }

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return from(this.handle(request, next))
  }

  async handle(request: HttpRequest<any>, next: HttpHandler) {
    const firebaseUser = this.firebaseAuth.currentUser;

    request = request.clone({
      url: request.url.startsWith(environment.apiUrl) ? request.url : `${environment.apiUrl}/${request.url}`,
      headers: new HttpHeaders({
        Authorization: firebaseUser ? `Bearer ${await firebaseUser?.getIdToken()}` : '',
      }),
    });

    return await lastValueFrom(next.handle(request));
  }
}
