import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import Volume from 'src/app/models/volume.model';

@Component({
  selector: 'app-volume',
  templateUrl: './volume.component.html',
  styleUrls: ['./volume.component.css']
})
export class VolumeComponent implements OnInit {

  volume = new Volume();

  constructor(
    private titleService: Title,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      Volume.find(params.id, { include: "manga" })
        .then(response => this.volume = response.data)
        .then(() => {
          this.titleService.setTitle(`${this.volume.manga.title} - Tome ${this.volume.number} | MangaJap`)
        })
        .catch(() => this.router.navigate(['**'], { skipLocationChange: true }));
    })
  }
}
