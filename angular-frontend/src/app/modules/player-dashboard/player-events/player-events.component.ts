import { Component, OnInit, Input } from '@angular/core';
import { EventService, Event } from '../../../services/event.service';

@Component({
  selector: 'app-player-events',
  templateUrl: './player-events.component.html'
})
export class PlayerEventsComponent implements OnInit {
  @Input() teamId!: number;
  events: Event[] = [];

  constructor(private eventService: EventService) {}

  ngOnInit(): void {
    this.loadEvents();
  }

  loadEvents(): void {
    this.eventService.getEventsByTeam(this.teamId).subscribe(events => {
      this.events = events;
    });
  }
}
