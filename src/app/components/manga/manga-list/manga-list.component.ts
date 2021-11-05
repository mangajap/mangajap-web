import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import Manga from 'src/app/models/manga.model';

@Component({
  selector: 'app-manga-list',
  templateUrl: './manga-list.component.html',
  styleUrls: ['./manga-list.component.css']
})
export class MangaListComponent implements OnInit {

  mangas: Manga[] = [];

  constructor(
    private titleService: Title,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle(`Mangas | MangaJap`);

    this.onSearch('');
  }


  onSearch(query: string) {
    Manga.findAll({
      sort: '-popularity',
      filter: { query: query }
    }).then(response => this.mangas = response.data);
  }
}
