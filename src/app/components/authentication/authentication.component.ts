import { Component, OnInit } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { Router } from '@angular/router';
import { User } from 'src/app/models/user.model';

@Component({
  selector: 'app-authentication',
  templateUrl: './authentication.component.html',
  styleUrls: ['./authentication.component.css']
})
export class AuthenticationComponent implements OnInit {

  user: User = new User();
  email: string = '';
  password: string = '';
  isLogin: boolean = false;
  isRegister: boolean = false;

  constructor(
    private router: Router,
    private firebaseAuth: AngularFireAuth,
  ) { }

  ngOnInit(): void {
    this.isLogin = this.router.url === '/login';
    // this.isRegister = this.router.url === '/register';
  }

  onLogin() {
    this.firebaseAuth.signInWithEmailAndPassword(this.email, this.password)
      .then((result) => {
        this.router.navigate(['/']);
      })
      .catch((error) => {
        window.alert(error.message);
      });
  }

  onRegister() {
    // // TODO: check pseudo et password
    // this.user.save().subscribe(response => {
    //   // TODO: check pas d'erreur
    //   this.mangajapApiService.login(this.user.pseudo, this.user.password).subscribe(response => {
    //     if (response.access_token) {
    //       this.router.navigate(['/']);
    //     }
    //   })
    // });
  }

}
