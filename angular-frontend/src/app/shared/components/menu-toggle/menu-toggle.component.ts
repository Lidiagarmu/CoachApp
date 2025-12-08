import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-menu-toggle',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './menu-toggle.component.html'
})
export class MenuToggleComponent {
  @Input() open: boolean = false;
  @Output() toggle = new EventEmitter<void>();

  onToggle() {
    this.toggle.emit();
  }
}
