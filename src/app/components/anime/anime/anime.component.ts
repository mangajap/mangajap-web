import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { Anime } from 'src/app/models/anime.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';

@Component({
  selector: 'app-anime',
  templateUrl: './anime.component.html',
  styleUrls: ['./anime.component.css']
})
export class AnimeComponent implements OnInit {

  anime: Anime = new Anime();

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    const id = +this.route.snapshot.paramMap.get('id');
    if (id) {
      Anime.find(id.toString(), {
        // include: ["episodes", "genres", "themes", "staff.people", "franchise.destination"],
      }).subscribe(response => {
        this.anime = response.data;
        this.titleService.setTitle(`${this.anime.title} | MangaJap`);
      });
    }
  }

}
