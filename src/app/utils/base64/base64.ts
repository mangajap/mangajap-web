export class Base64 {

    static encode(file: File, callback: (base64: string) => void) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
          callback(reader.result.toString());
        };
        reader.onerror = function (error) {
          throw `Exception: Unable to convert image to base 64\n${error}`;
        };
      }
}
