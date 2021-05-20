import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';
import { JsonApiParams } from 'src/app/utils/json-api/json-api-params';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  user: User = null;

  private timerSearch = null;

  constructor(
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    if (this.mangajapApiService.apiToken) this.user = new User();

    this.mangajapApiService.apiToken$.subscribe(token => {
      if (!token) {
        this.user = null;
      } else {
        User.findAll({
          filter: {
            self: "true"
          }
        }).subscribe(response => {
          if (response.data[0]) {
            this.user = response.data[0];
          } else {
            this.user = null;
          }
          this.mangajapApiService.selfUser = this.user;
        });
      }
    })
  }


  onSearch(query: string) {
    if (this.timerSearch) {
      clearTimeout(this.timerSearch);
    }

    this.timerSearch = setTimeout(() => {
      console.log(query)
    }, 1000);
  }

  logout() {
    this.mangajapApiService.logout();
  }
}
