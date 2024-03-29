import { NgModule } from '@angular/core';
import { AuthGuard, redirectLoggedInTo } from '@angular/fire/auth-guard';
import { RouterModule, Routes } from '@angular/router';

import { PrivacyPolicyComponent } from './pages/about/privacy-policy/privacy-policy.component';
import { AnimeComponent } from './pages/anime/anime/anime.component';
import { AnimeListComponent } from './pages/anime/anime-list/anime-list.component';
import { LoginComponent } from './pages/authentication/login/login.component';
import { RegisterComponent } from './pages/authentication/register/register.component';
import { HomeComponent } from './pages/home/home.component';
import { MangaComponent } from './pages/manga/manga/manga.component';
import { MangaListComponent } from './pages/manga/manga-list/manga-list.component';
import { NotFoundComponent } from './pages/not-found/not-found.component';
import { ProfileComponent } from './pages/profile/profile/profile.component';

const routes: Routes = [
  { path: '', pathMatch: 'full', component: HomeComponent },

  {
    path: 'login',
    component: LoginComponent,
    canActivate: [AuthGuard],
    data: { authGuardPipe: () => redirectLoggedInTo(['/']) },
  },
  {
    path: 'register',
    component: RegisterComponent,
    canActivate: [AuthGuard],
    data: { authGuardPipe: () => redirectLoggedInTo(['/']) },
  },

  { path: 'about/privacy-policy', component: PrivacyPolicyComponent },

  { path: 'anime', component: AnimeListComponent },
  { path: 'anime/:id', component: AnimeComponent },

  { path: 'manga', component: MangaListComponent },
  { path: 'manga/:id', component: MangaComponent },

  { path: 'profile/:id', component: ProfileComponent },

  { path: '**', component: NotFoundComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
