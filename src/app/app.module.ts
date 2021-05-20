import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { MangaJapApiInterceptor } from './services/mangajap-api.interceptor';
import { HeaderComponent } from './components/common/header/header.component';
import { FooterComponent } from './components/common/footer/footer.component';
import { HomeComponent } from './components/home/home.component';
import { MangaComponent } from './components/manga/manga/manga.component';
import { MangaSaveComponent } from './components/manga/manga-save/manga-save.component';
import { AnimeComponent } from './components/anime/anime/anime.component';
import { AnimeSaveComponent } from './components/anime/anime-save/anime-save.component';
import { GroupByPipe } from './utils/pipes/group-by.pipe';
import { RepeatPipe } from './utils/pipes/repeat.pipe';
import { FilterPipe } from './utils/pipes/filter.pipe';
import { EnumPipe } from './utils/pipes/enum.pipe';
import { MangaListComponent } from './components/manga/manga-list/manga-list.component';
import { AnimeListComponent } from './components/anime/anime-list/anime-list.component';
import { ProfileComponent } from './components/profile/profile/profile.component';
import { PrivacyPolicyComponent } from './components/about/privacy-policy/privacy-policy.component';
import { NotFoundComponent } from './components/not-found/not-found.component';
import { AuthenticationComponent } from './components/authentication/authentication.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    HomeComponent,
    MangaComponent,
    MangaSaveComponent,
    AnimeComponent,
    AnimeSaveComponent,
    GroupByPipe,
    RepeatPipe,
    FilterPipe,
    EnumPipe,
    MangaListComponent,
    AnimeListComponent,
    ProfileComponent,
    PrivacyPolicyComponent,
    NotFoundComponent,
    AuthenticationComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: MangaJapApiInterceptor, multi: true },
    GroupByPipe,
    RepeatPipe,
    FilterPipe,
    EnumPipe
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
