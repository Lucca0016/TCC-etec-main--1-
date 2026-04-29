<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Curso
{
    private PDO $pdo;
    private string $table = 'cursos';

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->ensureTableExists();

        if ($this->isEmpty()) {
            $this->seedDefaultCourses();
        }
    }

    public function listar(int $limite = 100): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . $this->getOrderByClause() . ' LIMIT ?');
            $stmt->bindValue(1, $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return $this->defaultCourses();
        }
    }

    public function listarFiltrado(array $filtros = [], int $limite = 50, int $offset = 0): array
    {
        try {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1=1';
            $params = [];

            if (!empty($filtros['search'])) {
                $params[':search'] = '%' . $filtros['search'] . '%';
                $sql .= ' AND (nome LIKE :search'
                    . ' OR descricao LIKE :search'
                    . ' OR descricao_completa LIKE :search'
                    . ' OR categoria LIKE :search'
                    . ' OR mercado_trabalho LIKE :search)';
            }

            if (!empty($filtros['modalidade'])) {
                $params[':modalidade'] = $filtros['modalidade'];
                $sql .= ' AND modalidade = :modalidade';
            }

            if (!empty($filtros['unidade'])) {
                $params[':unidade'] = '%' . $filtros['unidade'] . '%';
                $sql .= ' AND unidade LIKE :unidade';
            }

            if (!empty($filtros['categoria'])) {
                $params[':categoria'] = $filtros['categoria'];
                $sql .= ' AND categoria = :categoria';
            }

            if (!empty($filtros['duracao'])) {
                $params[':duracao'] = '%' . $filtros['duracao'] . '%';
                $sql .= ' AND duracao LIKE :duracao';
            }

            if (!empty($filtros['status'])) {
                $params[':status'] = $filtros['status'];
                $sql .= ' AND status = :status';
            }

            $countSql = 'SELECT COUNT(*) AS total FROM ' . $this->table . ' WHERE 1=1';
            if (!empty($filtros['search'])) {
                $countSql .= ' AND (nome LIKE :search'
                    . ' OR descricao LIKE :search'
                    . ' OR descricao_completa LIKE :search'
                    . ' OR categoria LIKE :search'
                    . ' OR mercado_trabalho LIKE :search)';
            }
            if (!empty($filtros['modalidade'])) {
                $countSql .= ' AND modalidade = :modalidade';
            }
            if (!empty($filtros['unidade'])) {
                $countSql .= ' AND unidade LIKE :unidade';
            }
            if (!empty($filtros['categoria'])) {
                $countSql .= ' AND categoria = :categoria';
            }
            if (!empty($filtros['duracao'])) {
                $countSql .= ' AND duracao LIKE :duracao';
            }
            if (!empty($filtros['status'])) {
                $countSql .= ' AND status = :status';
            }

            $sql .= $this->getOrderByClause() . ' LIMIT :limit OFFSET :offset';

            $countStmt = $this->pdo->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'dados' => $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [],
                'total' => $total,
                'pagina' => $limite > 0 ? (int)floor($offset / $limite) + 1 : 1,
                'total_paginas' => $limite > 0 ? (int)ceil($total / $limite) : 0,
            ];
        } catch (PDOException $e) {
            return [
                'dados' => [],
                'total' => 0,
                'pagina' => 1,
                'total_paginas' => 0,
            ];
        }
    }

    public function obterPorId(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
            $stmt->execute([$id]);
            $curso = $stmt->fetch(PDO::FETCH_ASSOC);
            return $curso ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function listarPorCategoria(string $categoria, int $limite = 50, int $offset = 0): array
    {
        return $this->listarFiltrado(['categoria' => $categoria], $limite, $offset);
    }

    public function listarPorUnidade(string $unidade, int $limite = 50, int $offset = 0): array
    {
        return $this->listarFiltrado(['unidade' => $unidade], $limite, $offset);
    }

    public function listarPorModalidade(string $modalidade, int $limite = 50, int $offset = 0): array
    {
        return $this->listarFiltrado(['modalidade' => $modalidade], $limite, $offset);
    }

    public function obterModalidades(): array
    {
        return $this->distinctColumnValues('modalidade');
    }

    public function obterUnidades(): array
    {
        return $this->splitDistinctValues($this->distinctColumnValues('unidade'));
    }

    public function obterCategorias(): array
    {
        return $this->distinctColumnValues('categoria');
    }

    public function obterStatus(): array
    {
        return $this->distinctColumnValues('status');
    }

    public function obterGradeCurricular(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT id, nome, grade_curricular FROM ' . $this->table . ' ORDER BY nome ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function all(): array
    {
        return $this->listar();
    }

    private function ensureTableExists(): void
    {
        try {
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                descricao_completa TEXT,
                duracao VARCHAR(100),
                modalidade VARCHAR(100),
                unidade TEXT,
                categoria VARCHAR(100),
                carga_horaria VARCHAR(100),
                turno VARCHAR(100),
                certificacao VARCHAR(255),
                beneficios TEXT,
                grade_curricular TEXT,
                disciplinas TEXT,
                mercado_trabalho TEXT,
                salario_medio VARCHAR(150),
                prerequisitos TEXT,
                status VARCHAR(100),
                imagem VARCHAR(500),
                popularidade INT DEFAULT 0,
                inscricoes_abertas TINYINT(1) DEFAULT 0,
                recomendado TINYINT(1) DEFAULT 0,
                destaque TINYINT(1) DEFAULT 0,
                novo TINYINT(1) DEFAULT 0,
                criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

            $this->ensureColumn('imagem', 'VARCHAR(500) DEFAULT NULL');
            $this->ensureColumn('categoria', 'VARCHAR(100) DEFAULT NULL');
            $this->ensureColumn('unidade', 'TEXT DEFAULT NULL');
            $this->ensureColumn('salario_medio', 'VARCHAR(150) DEFAULT NULL');
            $this->ensureColumn('grade_curricular', 'TEXT DEFAULT NULL');
            $this->ensureColumn('certificacao', 'VARCHAR(255) DEFAULT NULL');
            $this->ensureColumn('prerequisitos', 'TEXT DEFAULT NULL');
            $this->ensureColumn('duracao', 'VARCHAR(100) DEFAULT NULL');
            $this->ensureColumn('status', 'VARCHAR(100) DEFAULT NULL');
            $this->ensureColumn('descricao', 'TEXT DEFAULT NULL');
            $this->ensureColumn('descricao_completa', 'TEXT DEFAULT NULL');
            $this->ensureColumn('turno', 'VARCHAR(100) DEFAULT NULL');
            $this->ensureColumn('beneficios', 'TEXT DEFAULT NULL');
            $this->ensureColumn('disciplinas', 'TEXT DEFAULT NULL');
            $this->ensureColumn('mercado_trabalho', 'TEXT DEFAULT NULL');
            $this->ensureColumn('popularidade', 'INT DEFAULT 0');
            $this->ensureColumn('inscricoes_abertas', 'TINYINT(1) DEFAULT 0');
        } catch (PDOException $e) {
            // Se não for possível criar a tabela, não interrompemos a aplicação.
        }
    }

    private function ensureColumn(string $column, string $definition): void
    {
        if (!$this->columnExists($column)) {
            try {
                $this->pdo->exec('ALTER TABLE ' . $this->table . ' ADD COLUMN ' . $column . ' ' . $definition);
            } catch (PDOException $e) {
                // Falha na alteração, segue sem interromper.
            }
        }
    }

    private function isEmpty(): bool
    {
        try {
            $stmt = $this->pdo->query('SELECT COUNT(*) AS total FROM ' . $this->table);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0) === 0;
        } catch (PDOException $e) {
            return true;
        }
    }

    private function seedDefaultCourses(): void
    {
        foreach ($this->defaultCourses() as $curso) {
            $this->createCourse($curso);
        }
    }

    private function createCourse(array $curso): void
    {
        $columns = [
            'nome', 'descricao', 'descricao_completa', 'duracao', 'modalidade', 'unidade', 'categoria', 'carga_horaria',
            'turno', 'certificacao', 'beneficios', 'grade_curricular', 'disciplinas', 'mercado_trabalho',
            'salario_medio', 'prerequisitos', 'status', 'imagem', 'popularidade', 'inscricoes_abertas',
            'recomendado', 'destaque', 'novo'
        ];

        $placeholders = array_map(fn($field) => ':' . $field, $columns);
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($columns as $column) {
                $stmt->bindValue(':' . $column, $curso[$column] ?? null);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            // Não interrompe o fluxo se um registro não puder ser inserido.
        }
    }

    private function distinctColumnValues(string $column): array
    {
        try {
            if (!$this->columnExists($column)) {
                return [];
            }

            $stmt = $this->pdo->query('SELECT DISTINCT ' . $column . ' FROM ' . $this->table . ' WHERE ' . $column . ' IS NOT NULL AND ' . $column . ' <> "" ORDER BY ' . $column . ' ASC');
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
            return array_values(array_filter($rows, fn($value) => $value !== ''));
        } catch (PDOException $e) {
            return [];
        }
    }

    private function splitDistinctValues(array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            $parts = preg_split('/[,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($parts as $part) {
                $trimmed = trim($part);
                if ($trimmed !== '') {
                    $result[] = $trimmed;
                }
            }
        }
        return array_values(array_unique($result));
    }

    private function columnExists(string $column): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column');
            $stmt->execute(['table' => $this->table, 'column' => $column]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function getOrderByClause(): string
    {
        return $this->columnExists('popularidade') ? ' ORDER BY popularidade DESC, nome ASC' : ' ORDER BY nome ASC';
    }

    private function defaultCourses(): array
    {
        return [
            [
                'nome' => 'Desenvolvimento de Sistemas',
                'descricao' => 'Aprenda a criar soluções completas em software, web e aplicativos.',
                'descricao_completa' => 'Formação técnica em desenvolvimento de sistemas com foco em lógica, banco de dados, arquitetura de software e interfaces modernas. O curso prepara para atuar em empresas de tecnologia, agências digitais e áreas de TI.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo, Santo André',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '1200h',
                'turno' => 'Tarde',
                'certificacao' => 'Certificação Técnica em Desenvolvimento',
                'beneficios' => 'Laboratórios modernos, projetos reais, mentorias, suporte à empregabilidade',
                'grade_curricular' => 'Lógica de programação, Banco de dados, Desenvolvimento web, Redes, Engenharia de software, UX/UI, Segurança da informação',
                'disciplinas' => 'Lógica de programação, Banco de dados, Desenvolvimento web, UX/UI, Engenharia de software',
                'mercado_trabalho' => 'Alta demanda por desenvolvedores web, analistas de sistemas e engenheiros de software.',
                'salario_medio' => 'R$ 2.800 - R$ 6.200',
                'prerequisitos' => 'Ensino Médio completo, interesse por programação e lógica.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 95,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 1,
                'novo' => 0,
            ],
            [
                'nome' => 'Ciência de Dados',
                'descricao' => 'Domine estatística, machine learning e análise avançada de dados.',
                'descricao_completa' => 'Curso técnico voltado para gestão de dados, mineração, visualização e modelos preditivos. Excelente para quem quer atuar como analista de dados, cientista de dados ou consultor em inteligência de negócios.',
                'duracao' => '2 anos',
                'modalidade' => 'Híbrido',
                'unidade' => 'São Caetano, Diadema',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '1100h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Ciência de Dados',
                'beneficios' => 'Laboratório de analytics, cases de mercado, aulas em nuvem, apoio à carreira',
                'grade_curricular' => 'Estatística, Programação Python, Banco de dados, Machine learning, Visualização de dados, Big data, Ética em dados',
                'disciplinas' => 'Estatística, Python, Banco de dados, Machine learning, Big data',
                'mercado_trabalho' => 'Demanda crescente em empresas de tecnologia, finanças, saúde e varejo para análise de dados.',
                'salario_medio' => 'R$ 3.200 - R$ 7.000',
                'prerequisitos' => 'Ensino Médio completo, base em matemática e raciocínio lógico.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 92,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 1,
                'novo' => 1,
            ],
            [
                'nome' => 'Redes de Computadores',
                'descricao' => 'Aprenda segurança, infraestrutura e conectividade corporativa.',
                'descricao_completa' => 'Formação técnica focada em redes locais, protocolos, cabeamento estruturado e segurança da informação. Ideal para profissionais que atuam com infraestrutura de TI e suporte avançado.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'Santo André, Diadema',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '1200h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Redes',
                'beneficios' => 'Aulas com equipamentos de rede, laboratórios, parcerias com fornecedores, estágio',
                'grade_curricular' => 'Topologias de rede, Protocolos TCP/IP, Roteadores e switches, Segurança de redes, Virtualização, Wireless, Monitoramento',
                'disciplinas' => 'Redes, Segurança, Roteadores, Wireless, Virtualização',
                'mercado_trabalho' => 'Profissionais procurados em provedores, data centers e áreas de infraestrutura de TI.',
                'salario_medio' => 'R$ 2.500 - R$ 5.000',
                'prerequisitos' => 'Ensino Médio completo e conhecimento básico de informática.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 88,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Segurança da Informação',
                'descricao' => 'Especialize-se em proteção de dados, testes de invasão e compliance.',
                'descricao_completa' => 'Curso técnico em segurança cibernética, políticas de dados, auditoria e defesa contra ataques digitais. Preparação para carreiras em consultoria de segurança, SOC e governança de TI.',
                'duracao' => '18 meses',
                'modalidade' => 'EAD',
                'unidade' => 'São Bernardo do Campo, São Caetano',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '960h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em Segurança da Informação',
                'beneficios' => 'Aulas remotas, acesso a laboratórios virtuais, simulações de ataque, conteúdo atualizado',
                'grade_curricular' => 'Segurança de redes, criptografia, gestão de riscos, auditoria, defesa digital, compliance',
                'disciplinas' => 'Criptografia, Gestão de riscos, Segurança de redes, Auditoria',
                'mercado_trabalho' => 'Alta demanda para analistas de segurança, consultores e especialistas em proteção de dados.',
                'salario_medio' => 'R$ 3.500 - R$ 7.500',
                'prerequisitos' => 'Ensino Médio completo e familiaridade com redes.',
                'status' => 'Em breve',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 84,
                'inscricoes_abertas' => 0,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 1,
            ],
            [
                'nome' => 'Desenvolvimento Web',
                'descricao' => 'Crie sites, aplicações e experiências digitais responsivas.',
                'descricao_completa' => 'Curso técnico em desenvolvimento web para criar interfaces, aplicações e sistemas com HTML, CSS, JavaScript e frameworks modernos. Ideal para atuar em agências, startups e equipes de produto.',
                'duracao' => '18 meses',
                'modalidade' => 'Híbrido',
                'unidade' => 'Diadema, Santo André',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '950h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Desenvolvimento Web',
                'beneficios' => 'Projetos práticos, portfólio profissional, mentorias, ferramentas atuais',
                'grade_curricular' => 'HTML, CSS, JavaScript, Frameworks, UX/UI, APIs, Banco de dados',
                'disciplinas' => 'Front-end, Back-end, UX/UI, APIs, Banco de dados',
                'mercado_trabalho' => 'Grande demanda por desenvolvedores web, integradores e especialistas em UX.',
                'salario_medio' => 'R$ 2.700 - R$ 6.000',
                'prerequisitos' => 'Ensino Médio completo, vontade de aprender programação.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 93,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 1,
                'novo' => 0,
            ],
            [
                'nome' => 'Inteligência Artificial',
                'descricao' => 'Estude IA e machine learning para soluções automatizadas.',
                'descricao_completa' => 'Formação técnica em inteligência artificial, com foco em machine learning, redes neurais, processamento de linguagem natural e automação inteligente. Preparação para áreas de pesquisa e desenvolvimento em IA.',
                'duracao' => '2 anos',
                'modalidade' => 'EAD',
                'unidade' => 'São Paulo',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '1200h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em IA',
                'beneficios' => 'Laboratório de dados, projetos práticos, acesso a plataformas de IA, networking',
                'grade_curricular' => 'Machine learning, Redes neurais, Processamento de linguagem natural, Visão computacional, Ética em IA',
                'disciplinas' => 'Machine learning, NLP, Redes neurais, Visão computacional',
                'mercado_trabalho' => 'Alta demanda por engenheiros de IA, analistas de dados avançados e especialistas em automação.',
                'salario_medio' => 'R$ 4.000 - R$ 9.000',
                'prerequisitos' => 'Ensino Médio completo, lógica de programação e matemática.',
                'status' => 'Novo',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 90,
                'inscricoes_abertas' => 0,
                'recomendado' => 1,
                'destaque' => 1,
                'novo' => 1,
            ],
            [
                'nome' => 'Banco de Dados',
                'descricao' => 'Planeje, modele e administre bancos de dados corporativos.',
                'descricao_completa' => 'Curso técnico em banco de dados com foco em modelagem, SQL, administração, performance e segurança. Ideal para quem deseja trabalhar no núcleo de informação de empresas e sistemas.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo, São Caetano',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '1200h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Banco de Dados',
                'beneficios' => 'Laboratório de SQL, projetos com bancos reais, consultoria de carreira',
                'grade_curricular' => 'Modelagem de dados, SQL, Administração de SGBD, Performance, Segurança e backup',
                'disciplinas' => 'Modelagem, SQL, Administração de banco, Segurança',
                'mercado_trabalho' => 'Alta demanda em TI por DBAs, analistas de dados e engenheiros de dados.',
                'salario_medio' => 'R$ 3.000 - R$ 6.500',
                'prerequisitos' => 'Ensino Médio completo, lógica e orientação a dados.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 86,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Suporte Técnico',
                'descricao' => 'Prepare-se para atuar em atendimento, manutenção e suporte de TI.',
                'descricao_completa' => 'Curso técnico em suporte técnico com foco em hardware, software, atendimento ao cliente e manutenção preventiva. Ideal para quem busca começar na área de TI com perfil prático.',
                'duracao' => '1 ano',
                'modalidade' => 'Presencial',
                'unidade' => 'Santo André',
                'categoria' => 'Tecnologia',
                'carga_horaria' => '900h',
                'turno' => 'Manhã/Tarde',
                'certificacao' => 'Certificação Técnica em Suporte',
                'beneficios' => 'Oficinas de hardware, plantão de dúvidas, conexão com empresas',
                'grade_curricular' => 'Fundamentos de TI, Manutenção de computadores, Redes, Atendimento ao usuário, Segurança básica',
                'disciplinas' => 'Hardware, Software, Redes, Segurança, Atendimento',
                'mercado_trabalho' => 'Demanda sólida por técnicos em suporte, help desk e assistência técnica.',
                'salario_medio' => 'R$ 2.200 - R$ 4.200',
                'prerequisitos' => 'Ensino Médio completo e interesse por tecnologia.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 85,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Administração',
                'descricao' => 'Aprenda finanças, marketing e gestão de equipes.',
                'descricao_completa' => 'Curso técnico em administração voltado para gestão empresarial, finanças, marketing e processos administrativos. Ideal para atuação em empresas de todos os portes.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo, São Caetano',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '1000h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Administração',
                'beneficios' => 'Simuladores empresariais, networking, práticas de gestão, estágio',
                'grade_curricular' => 'Finanças, Recursos Humanos, Marketing, Logística, Planejamento, Empreendedorismo',
                'disciplinas' => 'Finanças, RH, Marketing, Logística, Planejamento',
                'mercado_trabalho' => 'Oportunidades em áreas administrativas, financeiro e apoio executivo.',
                'salario_medio' => 'R$ 2.200 - R$ 4.300',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 87,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Recursos Humanos',
                'descricao' => 'Formação em recrutamento, desenvolvimento e clima organizacional.',
                'descricao_completa' => 'Curso técnico em RH voltado para processos seletivos, treinamento, legislação trabalhista e cultura organizacional. Ideal para profissionais que querem atuar em gestão de pessoas.',
                'duracao' => '18 meses',
                'modalidade' => 'Híbrido',
                'unidade' => 'Santo André, Diadema',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '900h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em RH',
                'beneficios' => 'Workshop de liderança, processos seletivos, cases reais, estágio',
                'grade_curricular' => 'Recrutamento e seleção, Treinamento, Legislação trabalhista, Avaliação de desempenho, Comunicação',
                'disciplinas' => 'Recrutamento, Treinamento, Legislação, Avaliação, Comunicação',
                'mercado_trabalho' => 'Demanda por analistas de RH, recrutadores e consultores de pessoas.',
                'salario_medio' => 'R$ 2.400 - R$ 4.600',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 80,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Logística',
                'descricao' => 'Gestão de operações, transporte e cadeia de suprimentos.',
                'descricao_completa' => 'Curso técnico em logística voltado para planejamento de transporte, armazenagem, controle de estoque e inovação na cadeia de suprimentos. Ideal para e-commerce, indústrias e comércio.',
                'duracao' => '1 ano',
                'modalidade' => 'EAD',
                'unidade' => 'São Bernardo do Campo',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '700h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em Logística',
                'beneficios' => 'Aulas flexíveis, casos reais, plataforma EAD, simulações de operações',
                'grade_curricular' => 'Gestão de estoque, Transporte, Armazenagem, Distribuição, Planejamento',
                'disciplinas' => 'Estoque, Transporte, Armazenagem, Planejamento, CRM',
                'mercado_trabalho' => 'Oportunidades em centros de distribuição, transportadoras e empresas de comércio.',
                'salario_medio' => 'R$ 2.100 - R$ 4.100',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 78,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Marketing',
                'descricao' => 'Aprenda estratégias digitais, branding e comunicação de mercado.',
                'descricao_completa' => 'Curso técnico em marketing com foco em campanhas digitais, branding, comunicação e análise de resultados. Ideal para atuar em agências, startups e áreas de marketing de empresas.',
                'duracao' => '18 meses',
                'modalidade' => 'Híbrido',
                'unidade' => 'São Caetano, Santo André',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '920h',
                'turno' => 'Tarde',
                'certificacao' => 'Certificação Técnica em Marketing',
                'beneficios' => 'Cases reais, ferramentas digitais, projetos de moto, mentorias com profissionais',
                'grade_curricular' => 'Branding, Planejamento de mídia, Redes sociais, Conteúdo digital, Análise de dados',
                'disciplinas' => 'Branding, Mídia, Social media, Conteúdo, Analytics',
                'mercado_trabalho' => 'Procura por profissionais em marketing digital, comunicação e e-commerce.',
                'salario_medio' => 'R$ 2.300 - R$ 4.700',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1498079022511-d15614cb1c02?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 89,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Gestão Financeira',
                'descricao' => 'Capacite-se em planejamento financeiro e análise de investimentos.',
                'descricao_completa' => 'Curso técnico em gestão financeira com foco em contabilidade gerencial, fluxo de caixa, análise de custos e tomada de decisão. Ideal para empresas, consultorias e áreas administrativas.',
                'duracao' => '18 meses',
                'modalidade' => 'Presencial',
                'unidade' => 'Diadema',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '950h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Gestão Financeira',
                'beneficios' => 'Laboratório de finanças, estudos de caso, orientação de carreiras',
                'grade_curricular' => 'Contabilidade, Análise de custos, Fluxo de caixa, Investimentos, Planejamento financeiro',
                'disciplinas' => 'Contabilidade, Custos, Fluxo de caixa, Investimentos, Planejamento',
                'mercado_trabalho' => 'Procura por analistas financeiros, controladores e consultores de negócios.',
                'salario_medio' => 'R$ 2.500 - R$ 5.200',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Em breve',
                'imagem' => 'https://images.unsplash.com/photo-1493925417069-74372d70d10e?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 76,
                'inscricoes_abertas' => 0,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Empreendedorismo',
                'descricao' => 'Desenvolva competências para criar e gerir novos negócios.',
                'descricao_completa' => 'Curso técnico em empreendedorismo com foco em inovação, planejamento de negócios, gestão de startups e marketing. Ideal para quem quer abrir empresa ou liderar projetos empresariais.',
                'duracao' => '1 ano',
                'modalidade' => 'EAD',
                'unidade' => 'Santo André',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '750h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em Empreendedorismo',
                'beneficios' => 'Aulas flexíveis, cases de startups, mentorship, plano de negócio',
                'grade_curricular' => 'Modelagem de negócios, Planejamento estratégico, Negociação, Marketing, Finanças para startups',
                'disciplinas' => 'Modelagem, Planejamento, Negociação, Marketing, Finanças',
                'mercado_trabalho' => 'Útil para quem deseja empreender ou atuar em empresas inovadoras.',
                'salario_medio' => 'R$ 2.000 - R$ 4.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 82,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Comércio Exterior',
                'descricao' => 'Estude logística internacional, exportação e aduanas.',
                'descricao_completa' => 'Curso técnico em comércio exterior com foco em importação, exportação, legislação aduaneira e negociação internacional. Ideal para empresas exportadoras e operadores logísticos.',
                'duracao' => '1 ano',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo',
                'categoria' => 'Administração e Negócios',
                'carga_horaria' => '820h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Comércio Exterior',
                'beneficios' => 'Projetos práticos, conteúdo global, parcerias com empresas, mercado internacional',
                'grade_curricular' => 'Legislação aduaneira, Logística internacional, Negociação, Documentação, Comércio exterior',
                'disciplinas' => 'Aduaneiro, Logística, Negociação, Documentação, Comércio exterior',
                'mercado_trabalho' => 'Vagas em agentes de carga, trading companies e áreas de exportação.',
                'salario_medio' => 'R$ 2.400 - R$ 4.500',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1494172961521-33799ddd43a5?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 74,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Enfermagem',
                'descricao' => 'Formação técnica para atendimento clínico e cuidado humanizado.',
                'descricao_completa' => 'Curso técnico em enfermagem com foco em assistência hospitalar, primeiros socorros, biossegurança e atendimento humanizado. Ideal para atuar em unidades básicas, hospitais e clínicas.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Caetano, Diadema',
                'categoria' => 'Saúde',
                'carga_horaria' => '1400h',
                'turno' => 'Integral',
                'certificacao' => 'Certificação Técnica em Enfermagem',
                'beneficios' => 'Estágio em clínicas, laboratórios de simulação, apoio à empregabilidade',
                'grade_curricular' => 'Anatomia, Técnicas de enfermagem, Biossegurança, Farmacologia, Ética, Saúde comunitária',
                'disciplinas' => 'Anatomia, Técnicas, Biossegurança, Farmacologia, Ética',
                'mercado_trabalho' => 'Muito procurado em hospitais, clínicas e unidades básicas de saúde.',
                'salario_medio' => 'R$ 3.000 - R$ 5.500',
                'prerequisitos' => 'Ensino Médio completo e interesse pela saúde.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1581091012184-e58a5a7f7d8f?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 91,
                'inscricoes_abertas' => 1,
                'recomendado' => 1,
                'destaque' => 1,
                'novo' => 0,
            ],
            [
                'nome' => 'Farmácia',
                'descricao' => 'Especialize-se em indústria farmacêutica, análises e manipulação.',
                'descricao_completa' => 'Curso técnico em farmácia com foco em manipulação, análises clínicas, gestão de estoque farmacêutico e controle de qualidade. Ideal para atuar em laboratórios e farmácias.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'Santo André, Diadema',
                'categoria' => 'Saúde',
                'carga_horaria' => '1200h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Farmácia',
                'beneficios' => 'Aulas em laboratório, práticas de manipulação, supervisão farmacêutica',
                'grade_curricular' => 'Farmacologia, Manipulação, Controle de qualidade, Atenção farmacêutica, Bioquímica',
                'disciplinas' => 'Farmacologia, Manipulação, Qualidade, Bioquímica, Atenção farmacêutica',
                'mercado_trabalho' => 'Oportunidades em farmácias, indústrias e laboratórios de análises.',
                'salario_medio' => 'R$ 2.700 - R$ 5.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Em breve',
                'imagem' => 'https://images.unsplash.com/photo-1580281657521-0b1af64770a4?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 70,
                'inscricoes_abertas' => 0,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Radiologia',
                'descricao' => 'Aprenda técnicas de imagem e procedimentos radiológicos.',
                'descricao_completa' => 'Curso técnico de radiologia com foco em anatomia radiológica, operação de equipamentos de imagem e segurança do paciente. Preparação para atuação em clínicas, hospitais e centros de diagnóstico.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo',
                'categoria' => 'Saúde',
                'carga_horaria' => '1300h',
                'turno' => 'Integral',
                'certificacao' => 'Certificação Técnica em Radiologia',
                'beneficios' => 'Laboratório de radiologia, equipamentos modernos, treinamento em segurança',
                'grade_curricular' => 'Anatomia radiológica, Técnicas de imagem, Biossegurança, Processamento de imagens, Ética',
                'disciplinas' => 'Anatomia, Imagem, Biossegurança, Processamento, Ética',
                'mercado_trabalho' => 'Boa procura em clínicas de diagnóstico, hospitais e centros de imagem.',
                'salario_medio' => 'R$ 2.900 - R$ 5.800',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1580281657494-b5a8b11ef7d6?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 75,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Análises Clínicas',
                'descricao' => 'Estude técnicas laboratoriais e exames clínicos.',
                'descricao_completa' => 'Curso técnico em análises clínicas com foco em bioquímica, hematologia, microbiologia e interpretação de resultados. Ideal para atuação em laboratórios e serviços de saúde.',
                'duracao' => '18 meses',
                'modalidade' => 'Presencial',
                'unidade' => 'São Caetano',
                'categoria' => 'Saúde',
                'carga_horaria' => '980h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Análises Clínicas',
                'beneficios' => 'Práticas em laboratório, análises reais, acompanhamento profissional',
                'grade_curricular' => 'Bioquímica, Hematologia, Microbiologia, Imunologia, Ética e procedimentos',
                'disciplinas' => 'Bioquímica, Hematologia, Microbiologia, Imunologia',
                'mercado_trabalho' => 'Vagas em laboratórios clínicos, hospitais e centros de diagnóstico.',
                'salario_medio' => 'R$ 2.600 - R$ 5.200',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1526244434298-88fcbcb066b5?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 79,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Nutrição',
                'descricao' => 'Conheça alimentação, metabolismo e dietética aplicada.',
                'descricao_completa' => 'Curso técnico em nutrição com foco em saúde alimentar, dietoterapia, avaliação nutricional e educação em saúde. Ideal para atuar em clínicas, escolas e empresas de alimentação.',
                'duracao' => '1 ano',
                'modalidade' => 'Presencial',
                'unidade' => 'Diadema',
                'categoria' => 'Saúde',
                'carga_horaria' => '820h',
                'turno' => 'Manhã/Tarde',
                'certificacao' => 'Certificação Técnica em Nutrição',
                'beneficios' => 'Aulas práticas, projetos de saúde, orientação de carreira',
                'grade_curricular' => 'Nutrição humana, Metabolismo, Dietética, Saúde pública, Educação alimentar',
                'disciplinas' => 'Nutrição, Metabolismo, Dietética, Saúde pública, Educação alimentar',
                'mercado_trabalho' => 'Oportunidades em clínicas, consultórios, empresas de alimentação e programas de saúde.',
                'salario_medio' => 'R$ 2.700 - R$ 5.100',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Novo',
                'imagem' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 82,
                'inscricoes_abertas' => 0,
                'recomendado' => 1,
                'destaque' => 0,
                'novo' => 1,
            ],
            [
                'nome' => 'Mecatrônica',
                'descricao' => 'Integre eletrônica, mecânica e automação industrial.',
                'descricao_completa' => 'Curso técnico em mecatrônica com foco em sistemas automatizados, sensoriamento e integração mecânica-eletrônica. Ideal para indústrias e projetos de automação.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'Santo André, São Bernardo do Campo',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '1200h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Mecatrônica',
                'beneficios' => 'Laboratório automatizado, projetos industriais, parcerias com empresas',
                'grade_curricular' => 'Eletrônica, Mecânica, Automação, Programação de CLPs, Robótica',
                'disciplinas' => 'Eletrônica, Automação, Robótica, Programação de CLPs',
                'mercado_trabalho' => 'Oportunidades em indústria, manufatura e sistemas de automação.',
                'salario_medio' => 'R$ 3.000 - R$ 6.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 84,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Eletrotécnica',
                'descricao' => 'Estude instalações elétricas, projetos e manutenção industrial.',
                'descricao_completa' => 'Curso técnico em eletrotécnica voltado para instalações elétricas, automação industrial, manutenção e segurança. Ideal para indústrias, construtoras e empresas elétricas.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Caetano, Diadema',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '1200h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Eletrotécnica',
                'beneficios' => 'Oficina de elétrica, projetos práticos, aprendizado de normas, estágio',
                'grade_curricular' => 'Circuitos elétricos, Instalações prediais, Automação, Manutenção, Segurança',
                'disciplinas' => 'Circuitos, Instalações, Automação, Manutenção',
                'mercado_trabalho' => 'Profissionais demandados em eletrificação, manutenção e empresas industriais.',
                'salario_medio' => 'R$ 2.600 - R$ 5.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 82,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Mecânica',
                'descricao' => 'Especialize-se em manutenção, usinagem e processos produtivos.',
                'descricao_completa' => 'Curso técnico em mecânica com foco em manutenção industrial, desenho técnico, usinagem e processos de fabricação. Ideal para indústrias, oficinas e fábricas.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'Diadema',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '1200h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Mecânica',
                'beneficios' => 'Oficina bem equipada, projetos práticos, simulações industriais',
                'grade_curricular' => 'Desenho técnico, Metrologia, Usinagem, Caldeiraria, Manutenção de máquinas',
                'disciplinas' => 'Desenho, Metrologia, Usinagem, Manutenção',
                'mercado_trabalho' => 'Vagas em indústria, manutenção e produção metalmecânica.',
                'salario_medio' => 'R$ 2.500 - R$ 5.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1581093448790-4507aa8a3f55?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 79,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Automação Industrial',
                'descricao' => 'Aprenda sistemas automatizados, CLPs e controle industrial.',
                'descricao_completa' => 'Curso técnico em automação industrial com foco em CLPs, instrumentação, supervisórios e engenharia de controle. Ideal para indústrias e áreas de manutenção e operação.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '1200h',
                'turno' => 'Tarde',
                'certificacao' => 'Certificação Técnica em Automação Industrial',
                'beneficios' => 'Laboratório de CLP, projetos de automação, parcerias industriais',
                'grade_curricular' => 'CLPs, Instrumentação, Controle PID, Robótica, Manutenção',
                'disciplinas' => 'CLP, Instrumentação, Controle, Robótica, Manutenção',
                'mercado_trabalho' => 'Vagas em indústrias de produção, manufatura e automação.',
                'salario_medio' => 'R$ 2.800 - R$ 5.600',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 86,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Edificações',
                'descricao' => 'Conheça projetos, orçamentos e técnicas de construção.',
                'descricao_completa' => 'Curso técnico em edificações com foco em levantamento de obras, orçamentação, desenho técnico e gestão de canteiro. Ideal para profissionais da construção civil e projetos de engenharia.',
                'duracao' => '2 anos',
                'modalidade' => 'Presencial',
                'unidade' => 'Diadema',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '1200h',
                'turno' => 'Manhã',
                'certificacao' => 'Certificação Técnica em Edificações',
                'beneficios' => 'Aulas de projeto, canteiro simulado, cálculo estrutural, estágio',
                'grade_curricular' => 'Desenho técnico, Materiais de construção, Orçamentação, Execução de obras, Topografia',
                'disciplinas' => 'Desenho, Materiais, Orçamento, Obras, Topografia',
                'mercado_trabalho' => 'Busca por técnicos em construção, planejamento e fiscalização.',
                'salario_medio' => 'R$ 2.300 - R$ 4.800',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 75,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Design de Interiores',
                'descricao' => 'Crie ambientes funcionais e sofisticados para residências e empresas.',
                'descricao_completa' => 'Curso técnico em design de interiores com foco em projeto de ambientes, iluminação, acabamentos e estética. Ideal para quem deseja atuar em arquitetura de interiores e consultoria de design.',
                'duracao' => '1 ano',
                'modalidade' => 'Híbrido',
                'unidade' => 'São Caetano',
                'categoria' => 'Indústria e Engenharia',
                'carga_horaria' => '820h',
                'turno' => 'Tarde',
                'certificacao' => 'Certificação Técnica em Design de Interiores',
                'beneficios' => 'Portfólio profissional, workshops de projeto, parceria com escritórios',
                'grade_curricular' => 'Projeto de interiores, Materiais, Iluminação, Estética, Sustentabilidade',
                'disciplinas' => 'Projeto, Materiais, Iluminação, Sustentabilidade',
                'mercado_trabalho' => 'Oportunidades em escritórios de design, decoração e consultoria de ambientes.',
                'salario_medio' => 'R$ 2.300 - R$ 4.500',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 78,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Design Gráfico',
                'descricao' => 'Aprenda identidade visual, tipografia e criação digital.',
                'descricao_completa' => 'Curso técnico em design gráfico com foco em criação de marcas, material impresso, arte digital e produção visual. Preparação para atuar em agências, editoras e estúdios criativos.',
                'duracao' => '1 ano',
                'modalidade' => 'EAD',
                'unidade' => 'Santo André',
                'categoria' => 'Comunicação e Design',
                'carga_horaria' => '760h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em Design Gráfico',
                'beneficios' => 'Aulas com softwares, projetos visuais, portfólio profissional',
                'grade_curricular' => 'Identidade visual, Tipografia, Produção gráfica, Software de design, Comunicação visual',
                'disciplinas' => 'Identidade visual, Tipografia, Produção gráfica, Software',
                'mercado_trabalho' => 'Busca por designers em agências, empresas e comunicação digital.',
                'salario_medio' => 'R$ 2.200 - R$ 4.200',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 83,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Publicidade e Propaganda',
                'descricao' => 'Crie campanhas, mídia e estratégias para marcas.',
                'descricao_completa' => 'Curso técnico em publicidade e propaganda com foco em comunicação estratégica, mídia digital, criação de campanhas e planejamento publicitário. Ideal para agências, marketing e empresas.',
                'duracao' => '18 meses',
                'modalidade' => 'Híbrido',
                'unidade' => 'Diadema, São Caetano',
                'categoria' => 'Comunicação e Design',
                'carga_horaria' => '900h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Publicidade',
                'beneficios' => 'Projetos criativos, campanhas reais, networking com mercado',
                'grade_curricular' => 'Planejamento de mídia, Criação publicitária, Pesquisa, Estratégias digitais, Produção',
                'disciplinas' => 'Mídia, Criação, Pesquisa, Estratégia digital',
                'mercado_trabalho' => 'Demanda por profissionais de criação, planejamento e conteúdo publicitário.',
                'salario_medio' => 'R$ 2.300 - R$ 4.800',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1496317899792-9d7dbcd928a1?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 80,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Produção Multimídia',
                'descricao' => 'Domine áudio, vídeo e edição para conteúdo profissional.',
                'descricao_completa' => 'Curso técnico em produção multimídia com foco em vídeo, áudio, edição e conteúdo digital. Ideal para produtoras, estúdios e projetos de comunicação audiovisual.',
                'duracao' => '1 ano',
                'modalidade' => 'Presencial',
                'unidade' => 'São Bernardo do Campo',
                'categoria' => 'Comunicação e Design',
                'carga_horaria' => '820h',
                'turno' => 'Tarde',
                'certificacao' => 'Certificação Técnica em Produção Multimídia',
                'beneficios' => 'Estúdio completo, software profissional, criação de portfólio',
                'grade_curricular' => 'Captação de vídeo, Edição, Áudio, Pós-produção, Conteúdo digital',
                'disciplinas' => 'Vídeo, Áudio, Edição, Pós-produção, Conteúdo digital',
                'mercado_trabalho' => 'Oportunidades em produtoras, estúdios e comunicação digital.',
                'salario_medio' => 'R$ 2.400 - R$ 5.000',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 79,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Fotografia',
                'descricao' => 'Aprenda técnica, iluminação e edição de imagens.',
                'descricao_completa' => 'Curso técnico em fotografia com foco em composição, iluminação, edição e projetos fotográficos. Ideal para estúdios, eventos e produção de imagem editorial.',
                'duracao' => '1 ano',
                'modalidade' => 'EAD',
                'unidade' => 'São Caetano',
                'categoria' => 'Comunicação e Design',
                'carga_horaria' => '760h',
                'turno' => 'EAD',
                'certificacao' => 'Certificação Técnica em Fotografia',
                'beneficios' => 'Projetos práticos, edição digital, portfólio, aulas flexíveis',
                'grade_curricular' => 'Composição, Iluminação, Edição, Fotografia digital, Empreendedorismo fotográfico',
                'disciplinas' => 'Composição, Iluminação, Edição, Fotografia digital',
                'mercado_trabalho' => 'Vagas em estúdios, eventos, marketing e conteúdo visual.',
                'salario_medio' => 'R$ 2.200 - R$ 4.200',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 77,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
            [
                'nome' => 'Audiovisual',
                'descricao' => 'Aprenda produção de vídeo, som e narrativa para conteúdo.',
                'descricao_completa' => 'Curso técnico em audiovisual com foco em captação, edição, roteiro e produção de conteúdo para cinema e mídias digitais. Ideal para produtoras, estúdios e comunicação institucional.',
                'duracao' => '1 ano',
                'modalidade' => 'Presencial',
                'unidade' => 'Diadema',
                'categoria' => 'Comunicação e Design',
                'carga_horaria' => '820h',
                'turno' => 'Noite',
                'certificacao' => 'Certificação Técnica em Audiovisual',
                'beneficios' => 'Estúdio de gravação, edição profissional, portfólio, network',
                'grade_curricular' => 'Roteiro, Captação de imagem, Áudio, Edição, Produção audiovisual',
                'disciplinas' => 'Roteiro, Imagem, Áudio, Edição',
                'mercado_trabalho' => 'Oportunidades em cinema, TV, publicidade e conteúdo digital.',
                'salario_medio' => 'R$ 2.400 - R$ 5.200',
                'prerequisitos' => 'Ensino Médio completo.',
                'status' => 'Inscrições abertas',
                'imagem' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1000&q=80',
                'popularidade' => 81,
                'inscricoes_abertas' => 1,
                'recomendado' => 0,
                'destaque' => 0,
                'novo' => 0,
            ],
        ];
    }
}
