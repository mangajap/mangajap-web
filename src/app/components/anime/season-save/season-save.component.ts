import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Anime from 'src/app/models/anime.model';
import Season from 'src/app/models/season.model';

@Component({
  selector: 'app-season-save',
  templateUrl: './season-save.component.html',
  styleUrls: ['./season-save.component.css']
})
export class SeasonSaveComponent implements OnInit {

  season = new Season();

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe((params) => {
      if (params.id) {
        Season.find(params.id, { include: "anime" })
          .then((response) => this.season = response.data)
          .then(() => {
            this.titleService.setTitle(`${this.season.anime.title} - Saison ${this.season.number} - Modification | MangaJap`)
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      } else {
        Anime.find(params.animeId)
          .then((response) => this.season.anime = response.data)
          .then(() => {
            this.titleService.setTitle(`${this.season.anime.title} - Ajouter une saison | MangaJap`)
            this.season.number = this.season.anime.seasonCount + 1;
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      }
    });
  }


  submit() {
    if (!this.season.exists()) {
      this.season.create()
        .then(response => this.season.id = response.data.id)
        .then(() => this.router.navigate(['/anime', this.season.anime.id, 'season', this.season.id]))
        .catch(err => console.error(err))
    } else {
      this.season.update()
        .then(() => this.router.navigate(['/anime', this.season.anime.id, 'season', this.season.id]))
        .catch(err => console.error(err))
    }
  }
}
