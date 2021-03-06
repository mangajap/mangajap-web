import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import Anime from 'src/app/models/anime.model';

@Component({
  selector: 'app-anime-list',
  templateUrl: './anime-list.component.html',
  styleUrls: ['./anime-list.component.css']
})
export class AnimeListComponent implements OnInit {

  animes: Anime[] = [];

  constructor(
    private titleService: Title,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle(`Animes | MangaJap`);

    this.onSearch('');
  }

  onSearch(query: string) {
    Anime.findAll({
      sort: '-popularity',
      filter: {
        query: query
      }
    }).then(response => this.animes = response.data);
  }
}
