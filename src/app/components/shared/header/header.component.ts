import { Component } from '@angular/core';
import { Auth } from '@angular/fire/auth';
import User from 'src/app/models/user';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent {

  user: User | null = null;

  constructor(
    private auth: Auth,
  ) { }

  ngOnInit(): void {
    this.auth.onAuthStateChanged((firebaseUser) => {
      if (firebaseUser) {
        User.find(firebaseUser.uid)
          .then((response) => this.user = response.data);
      } else {
        this.user = null;
      }
    });
  }

  logout() {
    this.auth.signOut().then(() => { });
  }

}
