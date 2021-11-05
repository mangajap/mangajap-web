import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import Manga from 'src/app/models/manga.model';

@Component({
  selector: 'app-manga',
  templateUrl: './manga.component.html',
  styleUrls: ['./manga.component.css']
})
export class MangaComponent implements OnInit {

  manga: Manga = new Manga();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {

      Manga.find(params.id, {
        // include: ["volumes", "genres", "themes", "staff.people", "franchise.destination"],
      })
        .then(response => {
          this.manga = response.data;
          this.titleService.setTitle(`${this.manga.title} | MangaJap`);
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    })
  }

}
