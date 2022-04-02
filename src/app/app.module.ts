import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { AngularFireModule } from '@angular/fire';
import { AngularFireAuthModule } from '@angular/fire/auth';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { environment } from 'src/environments/environment';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { PrivacyPolicyComponent } from './components/about/privacy-policy/privacy-policy.component';
import { AnimeListComponent } from './components/anime/anime-list/anime-list.component';
import { AnimeSaveComponent } from './components/anime/anime-save/anime-save.component';
import { AnimeComponent } from './components/anime/anime/anime.component';
import { FooterComponent } from './components/common/footer/footer.component';
import { HeaderComponent } from './components/common/header/header.component';
import { HomeComponent } from './components/home/home.component';
import { MangaListComponent } from './components/manga/manga-list/manga-list.component';
import { MangaSaveComponent } from './components/manga/manga-save/manga-save.component';
import { MangaComponent } from './components/manga/manga/manga.component';
import { NotFoundComponent } from './components/not-found/not-found.component';
import { ProfileComponent } from './components/profile/profile/profile.component';
import { MangaJapApiInterceptor } from './services/mangajap-api.interceptor';
import { EnumPipe } from './utils/pipes/enum.pipe';
import { FilterNotPipe } from './utils/pipes/filter-not.pipe';
import { FilterPipe } from './utils/pipes/filter.pipe';
import { GroupByPipe } from './utils/pipes/group-by.pipe';
import { RepeatPipe } from './utils/pipes/repeat.pipe';
import { SafeUrlPipe } from './utils/pipes/safe-url.pipe';
import { LoginComponent } from './components/authentication/login/login.component';
import { RegisterComponent } from './components/authentication/register/register.component';
import { JsonApiTypePipe } from './utils/json-api/json-api-pipes';
import { PeopleComponent } from './components/people/people/people.component';
import { PeopleSaveComponent } from './components/people/people-save/people-save.component';
import { VolumeComponent } from './components/manga/volume/volume.component';
import { VolumeSaveComponent } from './components/manga/volume-save/volume-save.component';
import { SeasonComponent } from './components/anime/season/season.component';
import { SeasonSaveComponent } from './components/anime/season-save/season-save.component';


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
    FilterNotPipe,
    EnumPipe,
    SafeUrlPipe,
    JsonApiTypePipe,
    MangaListComponent,
    AnimeListComponent,
    ProfileComponent,
    PrivacyPolicyComponent,
    NotFoundComponent,
    LoginComponent,
    RegisterComponent,
    PeopleComponent,
    PeopleSaveComponent,
    VolumeComponent,
    VolumeSaveComponent,
    SeasonComponent,
    SeasonSaveComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    AngularFireModule.initializeApp(environment.firebase),
    AngularFireAuthModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: MangaJapApiInterceptor, multi: true },
    GroupByPipe,
    RepeatPipe,
    FilterPipe,
    FilterNotPipe,
    EnumPipe,
    SafeUrlPipe,
    JsonApiTypePipe,
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
