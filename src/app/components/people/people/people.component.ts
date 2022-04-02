import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import People from 'src/app/models/people.model';

@Component({
  selector: 'app-people',
  templateUrl: './people.component.html',
  styleUrls: ['./people.component.css']
})
export class PeopleComponent implements OnInit {

  people = new People();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      People.find(params.id)
        .then(response => this.people = response.data)
        .then(() => {
          if (this.people.pseudo)
            this.titleService.setTitle(`${this.people.pseudo} | MangaJap`);
          else
            this.titleService.setTitle(`${this.people.firstName} ${this.people.lastName} | MangaJap`);
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    })
  }
}
