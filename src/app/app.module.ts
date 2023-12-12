import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { initializeApp, provideFirebaseApp } from '@angular/fire/app';
import { provideAuth, getAuth } from '@angular/fire/auth';
import { AuthGuardModule } from '@angular/fire/auth-guard'; 
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { FooterComponent } from './components/shared/footer/footer.component';
import { HeaderComponent } from './components/shared/header/header.component';
import { PrivacyPolicyComponent } from './pages/about/privacy-policy/privacy-policy.component';
import { AnimeComponent } from './pages/anime/anime/anime.component';
import { AnimeListComponent } from './pages/anime/anime-list/anime-list.component';
import { LoginComponent } from './pages/authentication/login/login.component';
import { RegisterComponent } from './pages/authentication/register/register.component';
import { HomeComponent } from './pages/home/home.component';
import { MangaListComponent } from './pages/manga/manga-list/manga-list.component';
import { NotFoundComponent } from './pages/not-found/not-found.component';
import { ProfileComponent } from './pages/profile/profile/profile.component';
import { MangajapApiInterceptor } from './services/mangajap-api.interceptor';
import { JsonApiTypePipe } from './utils/json-api/json-api-type.pipe';
import { environment } from '../environments/environment';

@NgModule({
  declarations: [
    AnimeComponent,
    AnimeListComponent,
    AppComponent,
    FooterComponent,
    HeaderComponent,
    HomeComponent,
    JsonApiTypePipe,
    PrivacyPolicyComponent,
    LoginComponent,
    MangaListComponent,
    NotFoundComponent,
    ProfileComponent,
    RegisterComponent,
  ],
  imports: [
    AuthGuardModule,
    AppRoutingModule,
    BrowserModule,
    FormsModule,
    HttpClientModule,
    provideFirebaseApp(() => initializeApp(environment.firebase)),
    provideAuth(() => getAuth()),
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: MangajapApiInterceptor, multi: true },
  ],
  bootstrap: [AppComponent],
})
export class AppModule { }
