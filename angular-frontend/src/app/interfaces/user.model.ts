import { Team } from './team.model';
import { PlayerProfile } from './player-profile.model';

export interface User {
  id: number;
  email: string;
  nickname: string;
  fullName: string;
  roles: string[];
  age?: number;

  // Mantener team por compatibilidad si quieres
  team?: Team | null;

  // Perfil de jugador (nuevo campo)
  playerProfile?: PlayerProfile;
}
