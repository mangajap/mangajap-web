import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Manga } from 'src/app/models/manga.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';

@Component({
  selector: 'app-manga-list',
  templateUrl: './manga-list.component.html',
  styleUrls: ['./manga-list.component.css']
})
export class MangaListComponent implements OnInit {

  mangas: Manga[] = [];

  constructor(
    private titleService: Title,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle(`Mangas | MangaJap`);

    this.onSearch('');
  }


  onSearch(query: string) {
    Manga.findAll({
      sort: '-popularity',
      filter: { query: query }
    }).subscribe(response => this.mangas = response.data);
  }
}
