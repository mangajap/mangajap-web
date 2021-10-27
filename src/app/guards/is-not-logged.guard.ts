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
  ): Promise<boolean | UrlTree> {
    return this.isLogged.canActivate(route, state)
      .then((isLogged) => !isLogged)
      .catch(() => false);
  }

}
