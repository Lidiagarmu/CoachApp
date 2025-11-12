export interface Event {
  id: string;
  type: 'training' | 'match';

  // Campos comunes
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
  team?: string;
  teamShield?: string;
  opponent?: string;
  match_type?: string;
}
