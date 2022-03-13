import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import Anime from 'src/app/models/anime.model';
import Franchise from 'src/app/models/franchise.model';
import Genre from 'src/app/models/genre.model';
import Manga from 'src/app/models/manga.model';
import People from 'src/app/models/people.model';
import Staff from 'src/app/models/staff.model';
import Theme from 'src/app/models/theme.model';
import Volume from 'src/app/models/volume.model';
import Base64 from 'src/app/utils/base64/base64';

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

  mangaOrigin = Manga.Origin;
  mangaStatus = Manga.Status;
  mangaType = Manga.MangaType;
  staffRole = Staff.Role;
  franchiseRole = Franchise.Role;

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle("Ajouter un manga | MangaJap");

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
        Manga.find(params.id, {
          include: ["volumes", "genres", "themes", "staff.people", "franchises.destination"],
        }).then(response => {
          this.manga = response.data;
          this.titleService.setTitle(`${this.manga.title} - Modification | MangaJap`);
        });
      }
    });
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


  genreIsAdded(genre: Genre): boolean {
    return !!this.manga.genres.find(g => g.id === genre.id)
  }
  onGenreChecked(genre: Genre, checked: boolean) {
    if (checked) {
      this.manga.genres.push(genre);
    } else {
      this.manga.genres.splice(this.manga.genres.findIndex(g => g.id === genre.id), 1);
    }
  }
  createGenre() {
    const genre = new Genre();
    this.manga.genres.push(genre);
  }
  removeGenre(genre: Genre) {
    this.manga.genres.splice(this.manga.genres.indexOf(genre), 1);
  }

  themeIsAdded(theme: Theme): boolean {
    return !!this.manga.themes.find(t => t.id === theme.id)
  }
  onThemeChecked(theme: Theme, checked: boolean) {
    if (checked) {
      this.manga.themes.push(theme);
    } else {
      this.manga.themes.splice(this.manga.themes.findIndex(t => t.id === theme.id), 1);
    }
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
      })
    ]).then(([mangaResponse, animeResponse]) => {
      this.mediaQuery = [].concat(mangaResponse.data).concat(animeResponse.data)
        .filter(media => {
          if (media instanceof Manga) {
            return media.id !== this.manga.id;
          }
          return true;
        });
    });
  }


  submit() {
    if (!this.manga.exists()) {
      this.createInfo()
        .then(() => this.router.navigate(['/manga', this.manga.id]))
        .catch(err => console.error(err));
    } else {
      this.updateInfo()
        .then(() => this.router.navigate(['/manga', this.manga.id]))
        .catch(err => console.error(err));
    }
  }

  private async createInfo() {
    await Promise.all<any>([
      ...this.manga.genres
        .filter(genre => !genre.exists())
        .map(genre => genre.save()
          .then(response => genre.id = response.data.id)
        ),
      ...this.manga.themes
        .filter(theme => !theme.exists())
        .map(theme => theme.save()
          .then(response => theme.id = response.data.id)
        ),
      ...this.manga.staff
        .filter(staff => !staff.people.exists())
        .map(staff => staff.people.save()
          .then(response => staff.people.id = response.data.id)
        ),
    ]);

    await this.manga.save()
      .then(response => this.manga.id = response.data.id);

    await Promise.all<any>([
      ...this.manga.volumes.map(volume => {
        volume.manga = this.manga;
        return volume.save();
      }),
      ...this.manga.staff.map(staff => {
        staff.manga = this.manga;
        return staff.save();
      }),
      ...this.manga.franchise.map(franchise => {
        franchise.source = this.manga;
        return franchise.save();
      }),
    ]);
  }

  private async updateInfo() {
    await Promise.all<any>([
      ...this.manga.genres
        .filter(genre => !genre.exists())
        .map(genre => genre.save()
          .then(response => genre.id = response.data.id)
        ),
      ...this.manga.themes
        .filter(theme => !theme.exists())
        .map(theme => theme.save()
          .then(response => theme.id = response.data.id)
        ),
      ...this.manga.staff
        .filter(staff => !staff.people.exists())
        .map(staff => staff.people.save()
          .then(response => staff.people.id = response.data.id)
        ),
    ]);

    await Promise.all<any>([
      this.manga.save(),
      ...this.manga.volumes
        .filter(volume => !volume.exists() || volume.hasChanged())
        .map(volume => {
          if (!volume.exists()) {
            volume.manga = this.manga;
          }
          return volume;
        })
        .map(volume => volume.save()),
      ...this.manga.staff
        .filter(staff => !staff.exists() || staff.hasChanged())
        .map(staff => {
          if (!staff.exists()) {
            staff.manga = this.manga;
          }
          return staff;
        })
        .map(staff => staff.save()),
      ...this.manga.franchise
        .filter(franchise => !franchise.exists() || franchise.hasChanged())
        .map(franchise => {
          if (!franchise.exists()) {
            franchise.source = this.manga;
          }
          return franchise;
        })
        .map(franchise => franchise.save())
    ]);
  }
}
