import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { IsLoggedGuard } from './is-logged.guard';

@Injectable({
  providedIn: 'root'
})
export class IsNotLoggedGuard implements CanActivate {

  constructor(
    private isLogged: IsLoggedGuard
  ) { }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const isLogged = this.isLogged.canActivate(route, state);
    if (isLogged instanceof Observable) {
      return isLogged.pipe(
        map((value) => {
          return !value
        })
      );
    } else {
      return !this.isLogged.canActivate(route, state);
    }
  }

}
