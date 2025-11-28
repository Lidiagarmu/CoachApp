export interface EventTeam {
  id: number;
  name: string;
  shield?: string;
}

export interface Event {
  id: number; // usar number si Symfony devuelve id como entero
  type: 'training' | 'match';
  
  date: string;
  time: string;
  duration: number;
  location_name: string;
  location_url: string;
  images: string[];

  // Entrenamiento
  title?: string;
  training_type?: string;
  focus_area?: string;

  // Partido
  opponent?: string;
  match_type?: string;

  // Relación con equipo
  team?: EventTeam | null;

  // 🆕 Relación con jugador (opcional)
  playerId?: number;
}
