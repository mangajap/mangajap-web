import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Manga from 'src/app/models/manga.model';
import Volume from 'src/app/models/volume.model';
import Base64 from 'src/app/utils/base64/base64';
import Languages from 'src/app/utils/languages/languages';

@Component({
  selector: 'app-volume-save',
  templateUrl: './volume-save.component.html',
  styleUrls: ['./volume-save.component.css']
})
export class VolumeSaveComponent implements OnInit {

  volume = new Volume();

  languages = Languages.getLanguages();

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe((params) => {
      if (params.id) {
        Volume.find(params.id, { include: "manga" })
          .then((response) => this.volume = response.data)
          .then(() => {
            this.titleService.setTitle(`${this.volume.manga.title} - Tome ${this.volume.number} - Modification | MangaJap`)
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      } else {
        Manga.find(params.mangaId)
          .then((response) => this.volume.manga = response.data)
          .then(() => {
            this.titleService.setTitle(`${this.volume.manga.title} - Ajouter un tome | MangaJap`)
            this.volume.number = this.volume.manga.volumeCount + 1;
          })
          .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
      }
    });
  }


  onImageChanged(file: File | null) {
    if (file) {
      Base64.encode(file, (base64) => this.volume.coverImage = base64);
    } else {
      this.volume.coverImage = null;
    }
  }


  onTitleLanguageAdded() {
    this.volume.titles[''] = '';
  }
  onTitleLanguageChanged(index: number, language: string) {
    if (this.volume.titles[language]) {
      delete this.volume.titles[Object.keys(this.volume.titles)[index]];
    } else {
      this.volume.titles = Object.keys(this.volume.titles).reduce((acc, key, i) => {
        acc[i === index ? language : key] = this.volume.titles[key];
        return acc;
      }, {});
    }
  }
  onTitleLanguageRemoved(index: number) {
    delete this.volume.titles[Object.keys(this.volume.titles)[index]];
  }
  unsorted() { }


  submit() {
    if (!this.volume.exists()) {
      this.volume.create()
        .then(response => this.volume.id = response.data.id)
        .then(() => this.router.navigate(['/manga', this.volume.manga.id, 'volume', this.volume.id]))
        .catch(err => console.error(err))
    } else {
      this.volume.update()
        .then(() => this.router.navigate(['/manga', this.volume.manga.id, 'volume', this.volume.id]))
        .catch(err => console.error(err))
    }
  }
}
