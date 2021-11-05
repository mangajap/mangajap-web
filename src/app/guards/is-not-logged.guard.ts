import { Injectable } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { map, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class IsNotLoggedGuard implements CanActivate {

  constructor(
    private router: Router,
    private firebaseAuth: AngularFireAuth,
  ) { }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    return this.firebaseAuth.authState.pipe(
      map(user => !user),
      tap(isNotLogged => {
        if (!isNotLogged) {
          this.router.navigate(['/'])
        }
      }),
    );
  }

}
