import { Component, OnInit } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { Router } from '@angular/router';
import User from 'src/app/models/user.model';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  user: User = new User();
  email: string = '';
  password: string = '';

  constructor(
    private router: Router,
    private firebaseAuth: AngularFireAuth,
  ) { }

  ngOnInit(): void {
  }

  onRegister() {
    this.firebaseAuth.createUserWithEmailAndPassword(this.email, this.password)
      .then((result) => {
        this.user.id = result.user.uid;
        return this.user.save();
      })
      .then(() => this.router.navigate(['/']))
      .catch((error) => window.alert(error.message));
  }
  
}
