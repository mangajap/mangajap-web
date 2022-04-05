import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Anime from 'src/app/models/anime.model';
import Episode from 'src/app/models/episode.model';
import Season from 'src/app/models/season.model';
import Languages from 'src/app/utils/languages/languages';

@Component({
  selector: 'app-episode-save',
  templateUrl: './episode-save.component.html',
  styleUrls: ['./episode-save.component.css']
})
export class EpisodeSaveComponent implements OnInit {

  episode = new Episode();

  languages = Languages.getLanguages();
  episodeType = Episode.EpisodeType;

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe((params) => {
      if (params.id) {
        Episode.find(params.id, { include: ["season", "anime"] })
          .then((response) => this.episode = response.data)
          .then(() => {
            this.titleService.setTitle(`${this.episode.anime.title} - S${this.episode.season.number}E${this.episode.number} - Modification | MangaJap`)
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      } else {
        Promise.all([
          Anime.find(params.animeId),
          Season.find(params.seasonId),
        ])
          .then(([animeResponse, seasonResponse]) => {
            this.episode.anime = animeResponse.data;
            this.episode.season = seasonResponse.data;
            console.log(this.episode.season)
          })
          .then(() => {
            this.titleService.setTitle(`${this.episode.anime.title} - S${this.episode.season.number} - Ajouter un épisode | MangaJap`)
            this.episode.relativeNumber = this.episode.season.episodeCount + 1;
            this.episode.number = this.episode.anime.episodeCount + 1;
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      }
    });
  }


  onTitleLanguageAdded() {
    this.episode.titles[''] = '';
  }
  onTitleLanguageChanged(index: number, language: string) {
    if (this.episode.titles[language]) {
      delete this.episode.titles[Object.keys(this.episode.titles)[index]];
    } else {
      this.episode.titles = Object.keys(this.episode.titles).reduce((acc, key, i) => {
        acc[i === index ? language : key] = this.episode.titles[key];
        return acc;
      }, {});
    }
  }
  onTitleLanguageRemoved(index: number) {
    delete this.episode.titles[Object.keys(this.episode.titles)[index]];
  }
  unsorted() { }


  submit() {
    if (!this.episode.exists()) {
      this.episode.create()
        .then(response => this.episode.id = response.data.id)
        .then(() => this.router.navigate(['/anime', this.episode.anime.id, 'season', this.episode.season.id, 'episode', this.episode.id]))
        .catch(err => console.error(err))
    } else {
      this.episode.update()
        .then(() => this.router.navigate(['/anime', this.episode.anime.id, 'season', this.episode.season.id, 'episode', this.episode.id]))
        .catch(err => console.error(err))
    }
  }
}
