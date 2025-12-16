export interface EventTeam {
  id: number;
  name: string;
  shield?: string;
}

export interface Event {
  id: number; 
  type: 'training' | 'match';

  title?: string;
  description?: string;

  date: string;
  time: string;
  duration: number;

  location_name: string;
  location_url: string;

  images: string[];

  // Entrenamiento
  training_type?: 'campo' | 'gimnasio';  
  focus_area?: string;                           


  // Partido
  opponent?: string;
  match_type?: string;

  // Relación con equipo
  team?: EventTeam | null;

  // Relación con jugador (opcional)
  playerId?: number;
}
