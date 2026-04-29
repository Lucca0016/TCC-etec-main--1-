-- Executar diretamente no MySQL/MariaDB

ALTER TABLE noticias 
ADD COLUMN imagem_capa VARCHAR(255) NULL DEFAULT NULL AFTER conteudo,
ADD COLUMN status_carrossel TINYINT(1) NOT NULL DEFAULT 0 AFTER imagem_capa;

-- Adicionar coluna de link para livros (PDF ou URL de leitura)
ALTER TABLE biblioteca_livros
ADD COLUMN IF NOT EXISTS link_pdf VARCHAR(500) NULL DEFAULT NULL AFTER imagem_capa;

-- Garantir que a tabela usuarios tenha suporte para a lógica caso falte algo
-- (normalmente já presente através de: email, nome_completo, papel, senha_hash, ativo)
