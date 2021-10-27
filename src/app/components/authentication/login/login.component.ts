import { Component, OnInit } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  email: string = '';
  password: string = '';

  constructor(
    private router: Router,
    private firebaseAuth: AngularFireAuth,
  ) { }

  ngOnInit(): void {
  }

  onLogin() {
    this.firebaseAuth.signInWithEmailAndPassword(this.email, this.password)
      .then(() => this.router.navigate(['/']))
      .catch((error) => window.alert(error.message));
  }

}
