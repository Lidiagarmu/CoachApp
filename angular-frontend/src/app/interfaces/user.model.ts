export interface User {
  id: number;
  email: string;
  nickname: string;
  fullName: string;
  roles: string[];
  age?: number;
  team?: {
    id: number;
    name: string;
  } | null;
}
