import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { User } from 'src/app/models/user.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';
import { OAuth2ErrorBody } from 'src/app/utils/oauth2/oauth2-error-body';

@Component({
  selector: 'app-authentication',
  templateUrl: './authentication.component.html',
  styleUrls: ['./authentication.component.css']
})
export class AuthenticationComponent implements OnInit {

  user: User = new User();
  isLogin: boolean = false;
  isRegister: boolean = false;

  constructor(
    private router: Router,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    this.isLogin = this.router.url === '/login';
    // this.isRegister = this.router.url === '/register';
  }

  onLogin() {
    this.mangajapApiService.login(this.user.pseudo, this.user.password).subscribe(
      response => {
        if (response.access_token) {
          this.router.navigate(['/']);
        }
      },
      (error: OAuth2ErrorBody) => { 
        console.error(error);
      }
    );
  }

  onRegister() {
    // TODO: check pseudo et password
    this.user.save().subscribe(response => {
      // TODO: check pas d'erreur
      this.mangajapApiService.login(this.user.pseudo, this.user.password).subscribe(response => {
        if (response.access_token) {
          this.router.navigate(['/']);
        }
      })
    });
  }

}
