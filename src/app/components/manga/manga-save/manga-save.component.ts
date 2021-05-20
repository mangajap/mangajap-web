import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import { forkJoin } from 'rxjs';
import { mergeMap } from 'rxjs/operators';
import { Anime } from 'src/app/models/anime.model';
import { Franchise } from 'src/app/models/franchise.model';
import { Genre } from 'src/app/models/genre.model';
import { Manga } from 'src/app/models/manga.model';
import { People } from 'src/app/models/people.model';
import { Request } from 'src/app/models/request.model';
import { Staff } from 'src/app/models/staff.model';
import { Theme } from 'src/app/models/theme.model';
import { Volume } from 'src/app/models/volume.model';
import { MangajapApiService } from 'src/app/services/mangajap-api.service';
import { Base64 } from 'src/app/utils/base64/base64';
import { JsonApiParams } from 'src/app/utils/json-api/json-api-params';

@Component({
  selector: 'app-manga-save',
  templateUrl: './manga-save.component.html',
  styleUrls: ['./manga-save.component.css']
})
export class MangaSaveComponent implements OnInit {

  manga: Manga = new Manga();

  genres: Genre[] = [];
  themes: Theme[] = [];
  peoples: People[] = [];

  mediaQuery: any[] = [];

  private timerSearchPeople = null;
  private timerSearchFranchise = null;

  mangaOrigin = Manga.Origin;
  mangaStatus = Manga.Status;
  mangaType = Manga.MangaType;
  staffRole = Staff.Role;
  franchiseRole = Franchise.Role;

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
    private mangajapApiService: MangajapApiService
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle("Ajouter un manga | MangaJap");

    Genre.findAll({
      limit: 1000,
      sort: "title_fr"
    }).subscribe(response => this.genres = response.data);

    Theme.findAll({
      limit: 1000,
      sort: "title_fr"
    }).subscribe(response => this.themes = response.data);

    const id = +this.route.snapshot.paramMap.get('id');
    if (id) {
      Manga.find(id.toString(), {
        include: ["volumes", "genres", "themes", "staff.people", "franchise.destination"],
      }).subscribe(response => {
        this.manga = response.data;
        this.titleService.setTitle(`${this.manga.canonicalTitle} - Modification | MangaJap`);
      });
    }
  }


  updateCover(file: File) {
    Base64.encode(file, (base64) => this.manga.coverImage = base64);
  }

  updateBanner(file: File) {
    Base64.encode(file, (base64) => this.manga.bannerImage = base64);
  }


  updateVolumes() {
    if (this.manga.volumeCount < this.manga.volumes.length) {
      this.manga.volumes
        .filter(volume => volume.number > this.manga.volumeCount)
        .forEach(volume => this.manga.volumes.splice(this.manga.volumes.indexOf(volume), 1));
    } else {
      for (let number = 1; number <= this.manga.volumeCount; number++) {
        if (this.manga.volumes.filter(volume => volume.number == number).length == 0) {
          const volume = new Volume();
          volume.number = number;

          this.manga.volumes.push(volume);
        }
      }
    }
  }

  addGenre() {
    const genre = new Genre();
    genre.id = '';

    this.manga.genres.push(genre);
  }
  createGenre() {
    const genre = new Genre();

    this.manga.genres.push(genre);
  }
  removeGenre(genre: Genre) {
    this.manga.genres.splice(this.manga.genres.indexOf(genre), 1);
  }

  addTheme() {
    const theme = new Theme();
    theme.id = '';

    this.manga.themes.push(theme);
  }
  createTheme() {
    const theme = new Theme();

    this.manga.themes.push(theme);
  }
  removeTheme(theme: Theme) {
    this.manga.themes.splice(this.manga.themes.indexOf(theme), 1);
  }

  onSearchPeople(query: string) {
    if (query === '') {
      this.peoples = [];
      return;
    }

    if (this.timerSearchPeople) {
      clearTimeout(this.timerSearchPeople);
    }

    this.timerSearchPeople = setTimeout(() => {

      People.findAll({
        filter: {
          query: query
        }
      }).subscribe(response => this.peoples = response.data);

    }, 1000);
  }
  addStaff() {
    const staff = new Staff();
    staff.id = '';
    staff.people = new People();

    this.manga.staff.push(staff);
  }
  createStaff() {
    const staff = new Staff();
    staff.people = new People();

    this.manga.staff.push(staff);
  }
  removeStaff(staff: Staff) {
    this.manga.staff.splice(this.manga.staff.indexOf(staff), 1);
  }

  addFranchise() {
    this.manga.franchise.push(new Franchise());
  }
  removeFranchise(franchise: Franchise) {
    this.manga.franchise.splice(this.manga.franchise.indexOf(franchise), 1);
  }
  onSearchFranchise(query: string) {
    if (query === '') {
      this.mediaQuery = [];
      return;
    }

    if (this.timerSearchFranchise) {
      clearTimeout(this.timerSearchFranchise);
    }

    this.timerSearchFranchise = setTimeout(() => {

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
          .filter(media => media.id !== this.manga.id && typeof media === typeof this.manga);
      });

    }, 1000);
  }


  submit() {
    if (!this.manga.exists()) {
      this.createInfo();
    } else {
      this.updateInfo();
    }
  }

  private createInfo() {
    const manga$ = this.manga.save().pipe(
      mergeMap(mangaResponse => {
        const volumes$ = this.manga.volumes.map(volume => {
          volume.manga = mangaResponse.data;
          return volume.save();
        });

        const staff$ = this.manga.staff.map(staff => {
          staff.manga = mangaResponse.data;
          if (!staff.people.exists()) {
            return staff.people.save().pipe(
              mergeMap(peopleResponse => {
                staff.people = peopleResponse.data;
                return staff.save();
              })
            );
          } else {
            return staff.save();
          }
        });

        const franchises$ = this.manga.franchise.map(franchise => {
          franchise.source = mangaResponse.data;
          return franchise.save();
        });

        this.manga.id = mangaResponse.data.id;
        return forkJoin([].concat(volumes$).concat(staff$).concat(franchises$));
      })
    );

    manga$.subscribe({
      next: value => console.log(value),
      error: error => console.error(error),
      complete: () => this.router.navigate(['/manga', this.manga.id])
    });
  }

  private updateInfo() {
    // TODO: comparer avec les volumes à la fin pour voir lesquelles ont été supprimés
    // this.manga['raw'].relationships['volumes'].data.map(volume => console.log(volume));
    const manga$ = this.manga.save();

    const volumes$ = this.manga.volumes
      .filter(volume => !volume.exists() || volume.hasChanged())
      .map(volume => {
        if (!volume.exists()) {
          volume.manga = this.manga;
          return volume.save();
        } else if (volume.hasChanged()) {
          return volume.save();
        }
      });

    const staff$ = this.manga.staff
      .filter(staff => !staff.people.exists() || !staff.exists() || staff.hasChanged())
      .map(staff => {
        if (!staff.people.exists()) {
          return staff.people.save().pipe(
            mergeMap(peopleResponse => {
              staff.people = peopleResponse.data;
              if (!staff.exists()) {
                staff.manga = this.manga;
                return staff.save();
              } else if (staff.hasChanged()) {
                return staff.save();
              }
            })
          );
        } else if (!staff.exists()) {
          staff.manga = this.manga;
          return staff.save();
        } else if (staff.hasChanged()) {
          return staff.save();
        }
      });

    const franchises$ = this.manga.franchise
      .filter(franchise => !franchise.exists() || franchise.hasChanged())
      .map(franchise => {
        if (!franchise.exists()) {
          franchise.source = this.manga;
          return franchise.save();
        } else if (franchise.hasChanged()) {
          return franchise.save();
        }
      });

    forkJoin([].concat([manga$]).concat(volumes$).concat(staff$).concat(franchises$)).subscribe({
      next: value => console.log(value),
      error: error => console.error(error),
      complete: () => this.router.navigate(['/manga', this.manga.id])
    });
  }
}
