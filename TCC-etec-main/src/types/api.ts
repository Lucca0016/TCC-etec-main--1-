export interface Usuario {
  id: number;
  email: string;
  nome_completo: string;
  papel: 'admin' | 'secretaria' | 'aluno' | 'professor';
  ativo: boolean;
  criado_em: string;
}

export interface UsuarioForm {
  email: string;
  nome_completo: string;
  papel: 'admin' | 'secretaria' | 'aluno' | 'professor';
  senha?: string;
  ativo: boolean;
}

export interface Livro {
  id: number;
  titulo: string;
  autor: string;
  isbn: string;
  quantidade: number;
  quantidade_disponivel: number;
  criado_em: string;
}

export interface LivroForm {
  titulo: string;
  autor: string;
  isbn: string;
  quantidade: number;
}

export interface Noticia {
  id: number;
  titulo: string;
  conteudo: string;
  autor_id: number;
  autor_nome?: string;
  publicado: boolean;
  publicado_em: string;
  criado_em: string;
}

export interface NoticiaForm {
  titulo: string;
  conteudo: string;
  publicado: boolean;
}

export interface RegistroAuditoria {
  id: number;
  usuario_id: number;
  acao: string;
  meta?: Record<string, unknown>;
  criado_em: string;
}

export interface ApiResponse<T> {
  ok: boolean;
  data?: T;
  message?: string;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  pagina: number;
  por_pagina: number;
}
