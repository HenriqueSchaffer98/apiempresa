export interface Empresa {
  id?: number;
  nome: string;
  cnpj: string;
  endereco: string;
  created_at?: string;
  updated_at?: string;
}

export interface Funcionario {
  id?: number;
  login: string;
  nome: string;
  cpf: string;
  email: string;
  endereco: string;
  senha?: string;
  documento_path?: string;
  empresa_ids?: number[];
  created_at?: string;
  updated_at?: string;
}

export interface Cliente {
  id?: number;
  login: string;
  nome: string;
  cpf: string;
  email: string;
  endereco: string;
  senha?: string;
  documento_path?: string;
  created_at?: string;
  updated_at?: string;
}

export interface AuthResponse {
  access_token: string;
  token_type: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}
