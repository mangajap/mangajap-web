import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Season from 'src/app/models/season.model';

@Component({
  selector: 'app-season',
  templateUrl: './season.component.html',
  styleUrls: ['./season.component.css']
})
export class SeasonComponent implements OnInit {

  season = new Season();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      Season.find(params.id, { include: "anime" })
        .then(response => this.season = response.data)
        .then(() => {
          this.titleService.setTitle(`${this.season.anime.title} - Saison ${this.season.number} | MangaJap`)
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    })
  }
}
