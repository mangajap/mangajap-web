import { Component } from '@angular/core';
import { Auth, createUserWithEmailAndPassword } from '@angular/fire/auth';
import { Router } from '@angular/router';
import User from 'src/app/models/user';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {

  user: User = new User();
  email: string = '';
  password: string = '';

  constructor(
    private router: Router,
    private auth: Auth,
  ) { }

  onRegister() {
    createUserWithEmailAndPassword(this.auth, this.email, this.password)
      .then((result) => {
        this.user.id = result.user.uid;
        return this.user.save();
      })
      .then(() => this.router.navigate(['/']))
      .catch((error) => window.alert(error.message));
  }

}
