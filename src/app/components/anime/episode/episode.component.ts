import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Episode from 'src/app/models/episode.model';

@Component({
  selector: 'app-episode',
  templateUrl: './episode.component.html',
  styleUrls: ['./episode.component.css']
})
export class EpisodeComponent implements OnInit {

  episode = new Episode();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      Episode.find(params.id, { include: "season.anime" })
        .then(response => this.episode = response.data)
        .then(() => {
          this.titleService.setTitle(`${this.episode.season.anime.title} - S${this.episode.season.number}E${this.episode.number} | MangaJap`)
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    })
  }
}
