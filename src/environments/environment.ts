// This file can be replaced during build by using the `fileReplacements` array.
// `ng build --prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

export const environment = {
  production: false,
  apiUrl: 'http://localhost:5000',
  firebase: {
    apiKey: "AIzaSyBERviz4ObXOcBPCHiY8weoU_zdA8UNcIk",
    authDomain: "mangajap.firebaseapp.com",
    projectId: "mangajap",
    storageBucket: "mangajap.appspot.com",
    messagingSenderId: "765459541968",
    appId: "1:765459541968:web:fd5acd1ab2ba4d4c1193d5",
    measurementId: "G-P784KGM19T"
  },
};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.
