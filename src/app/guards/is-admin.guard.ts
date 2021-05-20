import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { MangajapApiService } from '../services/mangajap-api.service';
import { IsLoggedGuard } from './is-logged.guard';

@Injectable({
  providedIn: 'root'
})
export class IsAdminGuard implements CanActivate {

  constructor(
    private isLogged: IsLoggedGuard,
    private mangajapApiService: MangajapApiService
  ) { }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const selfUser = this.mangajapApiService.selfUser;
    return selfUser ? selfUser.isAdmin : this.isLogged.canActivate(route, state);
  }

}
