import { Component } from '@angular/core';
import { Title } from '@angular/platform-browser';
import Manga from 'src/app/models/manga';

@Component({
  selector: 'app-manga-list',
  templateUrl: './manga-list.component.html',
  styleUrls: ['./manga-list.component.css']
})
export class MangaListComponent {

  list: Manga[] = [];

  constructor(
    private titleService: Title,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle(`Mangas | MangaJap`);

    Manga.findAll({
      sort: '-popularity',
    }).then((response) => this.list = response.data);
  }


  onSearch(event: Event) {
    Manga.findAll({
      sort: '-popularity',
      filter: {
        query: (event.target as HTMLInputElement).value,
      },
    }).then((response) => this.list = response.data);
  }

}
