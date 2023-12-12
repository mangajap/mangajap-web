import { Component } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Anime from 'src/app/models/anime';

@Component({
  selector: 'app-anime',
  templateUrl: './anime.component.html',
  styleUrls: ['./anime.component.css']
})
export class AnimeComponent {

  anime: Anime = new Anime();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {

      Anime.find(params['id'], {
        include: ["seasons.episodes", "genres", "themes", "staff.people", "franchises.destination"],
      })
        .then(response => {
          this.anime = response.data;
          this.titleService.setTitle(`${this.anime.title} | MangaJap`);
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    });
  }

}
