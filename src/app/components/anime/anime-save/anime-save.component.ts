import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import Anime from 'src/app/models/anime.model';
import Episode from 'src/app/models/episode.model';
import Franchise from 'src/app/models/franchise.model';
import Genre from 'src/app/models/genre.model';
import Manga from 'src/app/models/manga.model';
import People from 'src/app/models/people.model';
import Season from 'src/app/models/season.model';
import Staff from 'src/app/models/staff.model';
import Theme from 'src/app/models/theme.model';
import { range } from 'src/app/utils/array/array';
import Base64 from 'src/app/utils/base64/base64';
import Countries, { Country } from 'src/app/utils/countries/countries';

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

  countries: Country[] = Countries.getCountries();
  animeStatus = Anime.Status;
  animeType = Anime.AnimeType;
  staffRole = Staff.Role;
  franchiseRole = Franchise.Role;

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle("Ajouter un anime | MangaJap");

    Genre.findAll({
      limit: 1000,
      sort: "title",
    }).then(response => this.genres = response.data);

    Theme.findAll({
      limit: 1000,
      sort: "title",
    }).then(response => this.themes = response.data);

    this.route.params.subscribe(params => {

      if (params.id) {
        Anime.find(params.id, {
          include: ["seasons.episodes", "genres", "themes", "staff.people", "franchises.destination"],
        }).then(response => {
          this.anime = response.data;
          this.titleService.setTitle(`${this.anime.title} - Modification | MangaJap`);
        });
      }
    });
  }


  onCoverImageChange(file: File | null) {
    if (file) {
      Base64.encode(file, (base64) => this.anime.coverImage = base64);
    } else {
      this.anime.coverImage = null;
    }
  }

  onBannerImageChange(file: File | null) {
    if (file) {
      Base64.encode(file, (base64) => this.anime.bannerImage = base64);
    } else {
      this.anime.bannerImage = null;
    }
  }

  onYoutubeVideoIdChange() {
    if (this.anime.youtubeVideoId.startsWith("http://") || this.anime.youtubeVideoId.startsWith("https://")) {
      const videoYoutubeId = new URL(this.anime.youtubeVideoId).searchParams.get("v");
      this.anime.youtubeVideoId = videoYoutubeId;
    }
  }

  onSeasonCountChange() {
    if (this.anime.seasonCount < this.anime.seasons.length) {
      this.anime.seasons.splice(
        this.anime.seasonCount,
        this.anime.seasons.length - this.anime.seasonCount
      );
    } else {
      this.anime.seasons.push(...range(this.anime.seasons.length + 1, this.anime.seasonCount)
        .map(number => {
          const season = new Season();
          season.number = number;
          return season;
        }));
    }
  }

  onSeasonEpisodeCountChange(season: Season) {
    if (season.episodeCount < season.episodes.length) {
      season.episodes.splice(
        season.episodeCount,
        season.episodes.length - season.episodeCount
      );
    } else {
      season.episodes.push(...range(season.episodes.length + 1, season.episodeCount)
        .map(relativeNumber => {
          const episode = new Episode();
          episode.seasonNumber = season.number;
          episode.relativeNumber = relativeNumber;
          return episode;
        }));
    }

    this.anime.seasons
      .reduce((acc, season) => acc.concat(season.episodes), [] as Episode[])
      .forEach((episode, i) => {
        episode.number = i + 1;
      });

    this.anime.episodeCount = this.anime.seasons
      .reduce((acc, season) => acc.concat(season.episodes), [] as Episode[])
      .length;
  }

  genreIsAdded(genre: Genre): boolean {
    return !!this.anime.genres.find(g => g.id === genre.id)
  }
  onGenreChecked(genre: Genre, checked: boolean) {
    if (checked) {
      this.anime.genres.push(genre);
    } else {
      this.anime.genres.splice(this.anime.genres.findIndex(g => g.id === genre.id), 1);
    }
  }
  createGenre() {
    const genre = new Genre();
    this.anime.genres.push(genre);
  }
  removeGenre(genre: Genre) {
    this.anime.genres.splice(this.anime.genres.indexOf(genre), 1);
  }

  themeIsAdded(theme: Theme): boolean {
    return !!this.anime.themes.find(t => t.id === theme.id)
  }
  onThemeChecked(theme: Theme, checked: boolean) {
    if (checked) {
      this.anime.themes.push(theme);
    } else {
      this.anime.themes.splice(this.anime.themes.findIndex(t => t.id === theme.id), 1);
    }
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
    }).then(response => this.peoples = response.data);
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
  onStaffAdded(peopleIndex: string) {
    const staff = new Staff();
    staff.id = '';
    staff.people = this.peoples[peopleIndex];

    this.anime.staff.push(staff);
  }


  onFranchiseSearch(query: string) {
    if (query === '') {
      this.mediaQuery = [];
      return;
    }

    Promise.all([
      Manga.findAll({
        filter: {
          query: query
        }
      }),
      Anime.findAll({
        filter: {
          query: query
        }
      }),
    ]).then(([mangaResponse, animeResponse]) => {
      this.mediaQuery = [].concat(mangaResponse.data).concat(animeResponse.data)
      .filter(media => !(media instanceof Anime && media.id === this.anime.id))
      .filter(media => this.anime.franchises.findIndex(franchise => {
        return franchise.destination.type === media.type && franchise.destination.id === media.id
      }) === -1);
    });
  }
  onFranchiseAdded(mediaIndex: string) {
    const franchise = new Franchise();
    franchise.destination = this.mediaQuery[mediaIndex];

    this.anime.franchises.push(franchise);
  }
  removeFranchise(franchise: Franchise) {
    this.anime.franchises.splice(this.anime.franchises.indexOf(franchise), 1);
  }



  submit() {
    if (!this.anime.exists()) {
      this.createInfo()
        .then(() => this.router.navigate(['/anime', this.anime.id]))
        .catch(err => console.error(err));
    } else {
      this.updateInfo()
        .then(() => this.router.navigate(['/anime', this.anime.id]))
        .catch(err => console.error(err));
    }
  }

  private async createInfo() {
    await Promise.all<any>([
      ...this.anime.genres
        .filter(genre => !genre.exists())
        .map(genre => genre.save()
          .then(response => genre.id = response.data.id)
        ),
      ...this.anime.themes
        .filter(theme => !theme.exists())
        .map(theme => theme.save()
          .then(response => theme.id = response.data.id)
        ),
      ...this.anime.staff
        .filter(staff => !staff.people.exists())
        .map(staff => staff.people.save()
          .then(response => staff.people.id = response.data.id)
        ),
    ]);

    await this.anime.save()
      .then(response => this.anime.id = response.data.id);

    await Promise.all<any>([
      ...this.anime.seasons
        .map(season => {
          season.anime = this.anime;
          return season.save()
            .then(response => season.id = response.data.id)
            .then(async () => {
              return await Promise.all(season.episodes
                .map(episode => {
                  episode.season = season;
                  episode.anime = this.anime;
                  return episode;
                })
                .map(episode => episode.save())
              )
            })
        }),
      ...this.anime.staff
        .map(staff => {
          staff.anime = this.anime;
          return staff.save();
        }),
      ...this.anime.franchises
        .map(franchise => {
          franchise.source = this.anime;
          return franchise.save();
        }),
    ]);
  }

  private async updateInfo() {
    await Promise.all<any>([
      ...this.anime.genres
        ?.filter(genre => !genre.exists())
        ?.map(genre => genre.save()
          .then(response => genre.id = response.data.id)
        ),
      ...this.anime.themes
        ?.filter(theme => !theme.exists())
        ?.map(theme => theme.save()
          .then(response => theme.id = response.data.id)
        ),
      ...this.anime.staff
        ?.filter(staff => !staff.people.exists())
        ?.map(staff => staff.people.save()
          .then(response => staff.people.id = response.data.id)
        ),
    ]);

    await Promise.all<any>([
      this.anime.save(),
      ...this.anime.seasons
        // TODO: if season has changed
        .map(season => {
          if (!season.exists()) {
            season.anime = this.anime;
          }
          return season;
        })
        .map(season => {
          return season.save()
            .then(response => season.id = response.data.id)
            .then(async () => {
              return await Promise.all(season.episodes
                .filter(episode => !episode.exists() || episode.hasChanged())
                .map(episode => {
                  if (!episode.exists()) {
                    episode.season = season;
                    episode.anime = this.anime;
                  }
                  return episode;
                })
                .map(episode => episode.save())
              )
            })
        }),
      ...this.anime.staff
        .filter(staff => !staff.exists() || staff.hasChanged())
        .map(staff => {
          if (!staff.exists()) {
            staff.anime = this.anime;
          }
          return staff;
        })
        .map(staff => staff.save()),
      ...this.anime.franchises
        .filter(franchise => !franchise.exists() || franchise.hasChanged())
        .map(franchise => {
          if (!franchise.exists()) {
            franchise.source = this.anime;
          }
          return franchise;
        })
        .map(franchise => franchise.save())
    ]);
  }
}
