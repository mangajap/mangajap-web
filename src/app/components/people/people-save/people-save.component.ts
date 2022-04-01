import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import People from 'src/app/models/people.model';
import Base64 from 'src/app/utils/base64/base64';

@Component({
  selector: 'app-people-save',
  templateUrl: './people-save.component.html',
  styleUrls: ['./people-save.component.css']
})
export class PeopleSaveComponent implements OnInit {

  people: People = new People();

  constructor(
    private titleService: Title,
    private router: Router,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.titleService.setTitle("Ajouter une personne | MangaJap");

    this.route.params.subscribe(params => {
      if (params.id) {
        People.find(params.id).then(response => {
          this.people = response.data;
        });
      }
    });
  }


  onImageChanged(file: File | null) {
    if (file) {
      Base64.encode(file, (base64) => this.people.image = base64);
    } else {
      this.people.image = null;
    }
  }


  submit() {
    if (!this.people.exists()) {
      this.people.create()
        .then(response => this.people.id = response.data.id)
        .then(() => this.router.navigate(['/people', this.people.id]))
        .catch(err => console.error(err))
    } else {
      this.people.update()
        .then(() => this.router.navigate(['/people', this.people.id]))
        .catch(err => console.error(err))
    }
  }

}
