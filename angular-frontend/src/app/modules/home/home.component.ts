import { AfterViewInit, Component, ElementRef, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './home.component.html',
})
export class HomeComponent implements AfterViewInit {
  @ViewChild('heroVideo') videoRef!: ElementRef<HTMLVideoElement>;
  isPlaying = true;

  constructor(private router: Router) {}

  ngAfterViewInit(): void {
    const video = this.videoRef.nativeElement;

    video.muted = true;
    video
      .play()
      .then(() => {
        this.isPlaying = true;
      })
      .catch((err) => {
        console.warn('Autoplay failed:', err);
        this.isPlaying = false;
      });
  }

  togglePlayback(): void {
    const video = this.videoRef.nativeElement;

    if (video.paused) {
      video.play();
      this.isPlaying = true;
    } else {
      video.pause();
      this.isPlaying = false;
    }
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }

  goToRegister() {
    this.router.navigate(['/register']);
  }
}
