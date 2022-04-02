import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PrivacyPolicyComponent } from './components/about/privacy-policy/privacy-policy.component';
import { AnimeListComponent } from './components/anime/anime-list/anime-list.component';
import { AnimeSaveComponent } from './components/anime/anime-save/anime-save.component';
import { AnimeComponent } from './components/anime/anime/anime.component';
import { LoginComponent } from './components/authentication/login/login.component';
import { RegisterComponent } from './components/authentication/register/register.component';
import { HomeComponent } from './components/home/home.component';
import { MangaListComponent } from './components/manga/manga-list/manga-list.component';
import { MangaSaveComponent } from './components/manga/manga-save/manga-save.component';
import { MangaComponent } from './components/manga/manga/manga.component';
import { VolumeSaveComponent } from './components/manga/volume-save/volume-save.component';
import { VolumeComponent } from './components/manga/volume/volume.component';
import { NotFoundComponent } from './components/not-found/not-found.component';
import { PeopleSaveComponent } from './components/people/people-save/people-save.component';
import { PeopleComponent } from './components/people/people/people.component';
import { ProfileComponent } from './components/profile/profile/profile.component';
import { IsAdminGuard } from './guards/is-admin.guard';
import { IsNotLoggedGuard } from './guards/is-not-logged.guard';

const routes: Routes = [
  { path: '', pathMatch: 'full', component: HomeComponent },

  { path: 'login', component: LoginComponent, canActivate: [IsNotLoggedGuard] },
  { path: 'register', component: RegisterComponent, canActivate: [IsNotLoggedGuard] },

  { path: 'about/privacy-policy', component: PrivacyPolicyComponent },

  { path: 'anime', component: AnimeListComponent },
  { path: 'anime/add', component: AnimeSaveComponent, canActivate: [IsAdminGuard] },
  { path: 'anime/:id', component: AnimeComponent },
  { path: 'anime/:id/edit', component: AnimeSaveComponent, canActivate: [IsAdminGuard] },

  { path: 'manga', component: MangaListComponent },
  { path: 'manga/add', component: MangaSaveComponent, canActivate: [IsAdminGuard] },
  { path: 'manga/:id', component: MangaComponent },
  { path: 'manga/:id/edit', component: MangaSaveComponent, canActivate: [IsAdminGuard] },
  { path: 'manga/:mangaId/volume/add', component: VolumeSaveComponent, canActivate: [IsAdminGuard] },
  { path: 'manga/:mangaId/volume/:id', component: VolumeComponent },
  { path: 'manga/:mangaId/volume/:id/edit', component: VolumeSaveComponent, canActivate: [IsAdminGuard] },

  { path: 'people/add', component: PeopleSaveComponent, canActivate: [IsAdminGuard] },
  { path: 'people/:id', component: PeopleComponent },
  { path: 'people/:id/edit', component: PeopleSaveComponent, canActivate: [IsAdminGuard] },

  { path: 'profile/:id', component: ProfileComponent },

  { path: '**', component: NotFoundComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
