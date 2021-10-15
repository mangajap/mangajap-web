import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { User } from 'src/app/models/user.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {

  user: User = new User();

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    this.route.params
      .subscribe(params => {
        
        const slug = params['slug'];
        if (slug) {
          User.findAll({
            filter: {
              slug: slug
            }
          }).then(response => {
            if (response.data[0]) {
              this.user = response.data[0];
              this.titleService.setTitle(`${this.user.pseudo} - Profil | MangaJap`);
            } else {
              console.error("user introuvable");
            }
          });
        }
      });
  }

}
