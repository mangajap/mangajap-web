import { Component } from '@angular/core';
import { Title } from '@angular/platform-browser';
import Anime from 'src/app/models/anime';

@Component({
  selector: 'app-anime-list',
  templateUrl: './anime-list.component.html',
  styleUrls: ['./anime-list.component.css']
})
export class AnimeListComponent {

  list: Anime[] = [];

  constructor(
    private titleService: Title,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle(`Anime | MangaJap`);

    Anime.findAll({
      sort: '-popularity',
    }).then((response) => this.list = response.data);
  }

  onSearch(event: Event) {
    Anime.findAll({
      sort: '-popularity',
      filter: {
        query: (event.target as HTMLInputElement).value,
      },
    }).then((response) => this.list = response.data);
  }

}
