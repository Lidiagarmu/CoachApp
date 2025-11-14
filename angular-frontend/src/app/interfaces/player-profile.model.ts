import { Team } from './team.model';

export interface PlayerProfile {
  id: number;
  team?: Team | null;
  position?: string;
  number?: number;
}
