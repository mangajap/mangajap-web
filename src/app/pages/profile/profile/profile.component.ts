import { Component } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import User from 'src/app/models/user';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent {

  user: User = new User();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      
      User.find(params['id']).then(response => {
        this.user = response.data;
        this.titleService.setTitle(`${this.user.pseudo} - Profil | MangaJap`);
      });
    });
  }
  
}
