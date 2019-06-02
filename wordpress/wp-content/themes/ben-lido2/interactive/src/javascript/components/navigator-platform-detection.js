export class NavigationPlatform {
  constructor() {
    this.platform = navigator.platform || undefined;
    this.html = document.querySelector("html");
  }
  init() {
    if (this.platform) {
      // console.log(this.platform);
      // console.log(this.html);

      if (this.platform === "Win32") {
        this.html.setAttribute(
          "data-navigator-platform",
          "benlido-windows-platform"
        );
      }
    }
  }
}
