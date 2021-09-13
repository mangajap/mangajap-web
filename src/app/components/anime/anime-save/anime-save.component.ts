import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import { forkJoin, of } from 'rxjs';
import { map, mergeMap, switchMap, tap } from 'rxjs/operators';
import { Anime } from 'src/app/models/anime.model';
import { Episode } from 'src/app/models/episode.model';
import { Franchise } from 'src/app/models/franchise.model';
import { Genre } from 'src/app/models/genre.model';
import { Manga } from 'src/app/models/manga.model';
import { People } from 'src/app/models/people.model';
import { Staff } from 'src/app/models/staff.model';
import { Theme } from 'src/app/models/theme.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';
import { Base64 } from 'src/app/utils/base64/base64';

@Component({
  selector: 'app-anime-save',
  templateUrl: './anime-save.component.html',
  styleUrls: ['./anime-save.component.css']
})
export class AnimeSaveComponent implements OnInit {

  anime: Anime = new Anime();

  genres: Genre[] = [];
  themes: Theme[] = [];
  peoples: People[] = [];

  mediaQuery: any[] = [];

  animeOrigin = Anime.Origin;
  animeStatus = Anime.Status;
  animeType = Anime.AnimeType;
  staffRole = Staff.Role;
  franchiseRole = Franchise.Role;

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle("Ajouter un anime | MangaJap");

    Genre.findAll({
      limit: 1000,
      sort: "title"
    }).subscribe(response => this.genres = response.data);

    Theme.findAll({
      limit: 1000,
      sort: "title"
    }).subscribe(response => this.themes = response.data);

    const id = +this.route.snapshot.paramMap.get('id');
    if (id) {
      Anime.find(id.toString(), {
        include: ["episodes", "genres", "themes", "staff.people", "franchise.destination"],
      }).subscribe(response => {
        this.anime = response.data;
        this.titleService.setTitle(`${this.anime.title} - Modification | MangaJap`);
      });
    }
  }


  updateCover(file: File) {
    Base64.encode(file, (base64) => this.anime.coverImage = base64);
  }

  updateYoutubeVideo() {
    if (this.anime.youtubeVideoId.startsWith("http://") || this.anime.youtubeVideoId.startsWith("https://")) {
      const videoYoutubeId = new URL(this.anime.youtubeVideoId).searchParams.get("v");
      this.anime.youtubeVideoId = videoYoutubeId;
    }
  }

  updateEpisodes(seasonNumber: number, episodeCount: number) {
    const seasonEpisodes = this.anime.episodes.filter(episode => episode.seasonNumber === seasonNumber);

    if (episodeCount < seasonEpisodes.length) {
      seasonEpisodes
        .filter(episode => episode.relativeNumber > episodeCount)
        .forEach(episode => this.anime.episodes.splice(this.anime.episodes.indexOf(episode), 1));
    } else {
      for (let relativeNumber = 1; relativeNumber <= episodeCount; relativeNumber++) {
        if (seasonEpisodes
          .filter(episode => episode.relativeNumber === relativeNumber)
          .length == 0) {
          const episode = new Episode();
          episode.seasonNumber = seasonNumber;
          episode.relativeNumber = relativeNumber;

          this.anime.episodes.push(episode);
        }
      }
    }

    this.anime.episodes.sort((a, b) => a.seasonNumber - b.seasonNumber || a.relativeNumber - b.relativeNumber);

    this.anime.episodes.forEach((episode, index) => episode.number = index + 1);

    this.anime.episodeCount = this.anime.episodes.length;
  }

  addGenre() {
    const genre = new Genre();
    genre.id = '';

    this.anime.genres.push(genre);
  }
  createGenre() {
    const genre = new Genre();

    this.anime.genres.push(genre);
  }
  removeGenre(genre: Genre) {
    this.anime.genres.splice(this.anime.genres.indexOf(genre), 1);
  }

  addTheme() {
    const theme = new Theme();
    theme.id = '';

    this.anime.themes.push(theme);
  }
  createTheme() {
    const theme = new Theme();

    this.anime.themes.push(theme);
  }
  removeTheme(theme: Theme) {
    this.anime.themes.splice(this.anime.themes.indexOf(theme), 1);
  }

  onSearchPeople(query: string) {
    if (query === '') {
      this.peoples = [];
      return;
    }

    People.findAll({
      filter: {
        query: query
      }
    }).subscribe(response => this.peoples = response.data);
  }
  addStaff() {
    const staff = new Staff();
    staff.id = '';
    staff.people = new People();

    this.anime.staff.push(staff);
  }
  createStaff() {
    const staff = new Staff();
    staff.people = new People();

    this.anime.staff.push(staff);
  }
  removeStaff(staff: Staff) {
    this.anime.staff.splice(this.anime.staff.indexOf(staff), 1);
  }

  addFranchise() {
    this.anime.franchise.push(new Franchise());
  }
  removeFranchise(franchise: Franchise) {
    this.anime.franchise.splice(this.anime.franchise.indexOf(franchise), 1);
  }
  onSearchFranchise(query: string) {
    if (query === '') {
      this.mediaQuery = [];
      return;
    }

    const mangas$ = Manga.findAll({
      filter: {
        query: query
      }
    });
    const animes$ = Anime.findAll({
      filter: {
        query: query
      }
    });

    forkJoin([mangas$, animes$]).subscribe(([mangaResponse, animeResponse]) => {
      this.mediaQuery = [].concat(mangaResponse.data).concat(animeResponse.data)
        .filter(media => media.id !== this.anime.id && typeof media === typeof this.anime);
    });
  }


  submit() {
    if (!this.anime.id) {
      this.createInfo();
    } else {
      this.updateInfo();
    }
  }

  private createInfo() {
    forkJoin(
      []
        .concat(of(1))
        .concat(this.anime.genres?.filter(genre => !genre.exists()).map(genre => genre.save().pipe(
          map(response => {
            genre.id = response.data.id
            return response
          })
        )))
        .concat(this.anime.themes?.filter(theme => !theme.exists()).map(theme => theme.save().pipe(
          map(response => {
            theme.id = response.data.id
            return response
          })
        )))
    ).pipe(
      switchMap(() => this.anime.save().pipe(
        tap(response => this.anime.id = response.data.id),
        mergeMap(() => forkJoin(
          []
            .concat(this.anime.episodes.map(episode => {
              episode.anime = this.anime;
              return episode.save();
            }))
            .concat(this.anime.staff.map(staff => {
              staff.anime = this.anime;
              if (!staff.people.id) {
                return staff.people.save().pipe(
                  mergeMap(peopleResponse => {
                    staff.people = peopleResponse.data;
                    return staff.save();
                  })
                );
              } else {
                return staff.save();
              }
            }))
            .concat(this.anime.franchise.map(franchise => {
              franchise.source = this.anime;
              return franchise.save();
            }))
        ))
      ))
    ).subscribe({
      next: value => console.log(value),
      error: error => console.error(error),
      complete: () => this.router.navigate(['/anime', this.anime.id])
    });
  }

  private updateInfo() {
    forkJoin(
      []
        .concat(of(1))
        .concat(this.anime.genres?.filter(genre => !genre.exists()).map(genre => genre.save().pipe(
          map(response => {
            genre.id = response.data.id
            return response
          })
        )))
        .concat(this.anime.themes?.filter(theme => !theme.exists()).map(theme => theme.save().pipe(
          map(response => {
            theme.id = response.data.id
            return response
          })
        )))
    ).pipe(
      mergeMap(() => forkJoin(
        []
          .concat(this.anime.save())
          .concat(this.anime.episodes
            .filter(episode => !episode.id || episode.hasChanged())
            .map(episode => {
              if (!episode.id) {
                episode.anime = this.anime;
                return episode.save();
              } else if (episode.hasChanged()) {
                return episode.save();
              }
            }))
          .concat(this.anime.staff
            .filter(staff => !staff.people.id || !staff.id || staff.hasChanged())
            .map(staff => {
              if (!staff.people.id) {
                return staff.people.save().pipe(
                  mergeMap(peopleResponse => {
                    staff.people = peopleResponse.data;
                    if (!staff.id) {
                      staff.anime = this.anime;
                      return staff.save();
                    } else if (staff.hasChanged()) {
                      return staff.save();
                    }
                  })
                );
              } else {
                if (!staff.id) {
                  staff.anime = this.anime;
                  return staff.save();
                } else if (staff.hasChanged()) {
                  return staff.save();
                }
              }
            }))
          .concat(this.anime.franchise
            .filter(franchise => !franchise.id || franchise.hasChanged())
            .map(franchise => {
              console.log(franchise.hasChanged());
              if (!franchise.id) {
                franchise.source = this.anime;
                return franchise.save();
              } else if (franchise.hasChanged()) {
                return franchise.save();
              }
            }))
      ))
    ).subscribe(
      value => console.log(value),
      error => console.error(error),
      () => this.router.navigate(['/anime', this.anime.id])
    );
  }
}
