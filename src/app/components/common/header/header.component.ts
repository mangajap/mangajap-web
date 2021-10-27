import { Component, OnInit } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/auth';
import { User } from 'src/app/models/user.model';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  user: User = null;

  constructor(
    private firebaseAuth: AngularFireAuth,
  ) { }

  ngOnInit(): void {
    this.firebaseAuth.authState
      .subscribe(firebaseUser => {
        if (firebaseUser) {
          User.findAll({
            filter: {
              self: "true"
            }
          }).then(response => {
            if (response.data[0]) {
              this.user = response.data[0];
            } else {
              this.user = null;
            }
          });
        } else {
          this.user = null;
        }
      });
  }


  private timerSearch = null;
  onSearch(query: string) {
    if (this.timerSearch) {
      clearTimeout(this.timerSearch);
    }

    this.timerSearch = setTimeout(() => {
      console.log(query)
    }, 1000);
  }

  logout() {
    this.firebaseAuth.signOut().then(() => { });
  }
}
