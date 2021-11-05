import { Injectable } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { from, Observable, of } from 'rxjs';
import { switchMap, map, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class IsAdminGuard implements CanActivate {

  constructor(
    private router: Router,
    private firebaseAuth: AngularFireAuth,
  ) { }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    return this.firebaseAuth.authState.pipe(
      switchMap(user => user ? from(user.getIdTokenResult()) : of(false)),
      map(result => typeof result === 'boolean' ? result : result.claims.isAdmin),
      tap(isAdmin => {
        if (!isAdmin) {
          this.router.navigate(['/'])
        }
      }),
    );
  }

}
