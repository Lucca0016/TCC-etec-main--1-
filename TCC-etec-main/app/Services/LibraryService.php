<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Student;
use PDO;
use RuntimeException;

class LibraryService
{
    private PDO $pdo;
    private Student $students;
    private string $offlineDir;

    public function __construct(?PDO $pdo = null, ?Student $student = null, ?string $offlineDir = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->students = $student ?? new Student($this->pdo);
        $this->offlineDir = $offlineDir ?? storage_path('library');
        if (!is_dir($this->offlineDir)) {
            @mkdir($this->offlineDir, 0775, true);
        }
    }

    public function searchBooks(?string $term, int $limit = 120): array
    {
        $sql = 'SELECT id, titulo, autor, genero, ano_publicacao, ano, copias_disponiveis, copias_total, sinopse FROM biblioteca_livros';
        $params = [];
        if ($term) {
            $sql .= ' WHERE titulo LIKE :term OR autor LIKE :term OR genero LIKE :term';
            $params[':term'] = '%' . $term . '%';
        }
        $sql .= ' ORDER BY titulo ASC LIMIT ' . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function requestLoan(int $userId, int $bookId, int $days = 14): array
    {
        if ($userId <= 0 || $bookId <= 0) {
            throw new RuntimeException('Usuário ou livro inválido.');
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('SELECT id, titulo, copias_disponiveis FROM biblioteca_livros WHERE id = ? FOR UPDATE');
            $stmt->execute([$bookId]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$book) {
                throw new RuntimeException('Livro não encontrado.');
            }

            if ((int)$book['copias_disponiveis'] <= 0) {
                throw new RuntimeException('Não há cópias disponíveis para empréstimo.');
            }

            $dueDate = (new \DateTimeImmutable('+ ' . max(1, $days) . ' days'))->format('Y-m-d H:i:s');

            $stmt = $this->pdo->prepare('INSERT INTO biblioteca_emprestimos (usuario_id, livro_id, emprestado_em, vencimento_em, status)
                VALUES (:uid, :livro, NOW(), :due, :status)');
            $stmt->execute([
                ':uid' => $userId,
                ':livro' => $bookId,
                ':due' => $dueDate,
                ':status' => 'solicitado',
            ]);

            $loanId = (int)$this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare('UPDATE biblioteca_livros SET copias_disponiveis = copias_disponiveis - 1 WHERE id = ?');
            $stmt->execute([$bookId]);

            $this->pdo->commit();

            return [
                'ok' => true,
                'emprestimo_id' => $loanId,
                'livro' => $book,
                'status' => 'solicitado',
            ];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function activeLoans(int $userId): array
    {
        return $this->students->activeLoans($userId);
    }

    public function loanHistory(int $userId, int $limit = 10): array
    {
        return $this->students->loanHistory($userId, $limit);
    }

    public function solicitations(int $userId): array
    {
        return $this->students->solicitations($userId);
    }

    public function mergeOfflineData(int $userId, array &$active, array &$history): void
    {
        $offline = $this->loadOfflineLoans($userId);
        foreach ($offline as $loan) {
            $status = strtolower((string)($loan['status'] ?? 'solicitado'));
            $entry = [
                'titulo' => $loan['titulo'] ?? ('#' . ($loan['livro_id'] ?? '')),
                'status_real' => $status,
                'status' => $status,
                'emprestado_em' => $loan['data'] ?? null,
                'vencimento_em' => null,
                'devolvido_em' => null,
            ];
            if (in_array($status, ['solicitado', 'pendente_sync'], true)) {
                $active[] = $entry;
            } else {
                $history[] = $entry;
            }
        }
    }

    public function saveOfflineLoan(int $userId, array $loan): void
    {
        $data = $this->loadOfflineLoans($userId);
        $data[] = $loan;
        file_put_contents($this->offlineFile($userId), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function loadOfflineLoans(int $userId): array
    {
        $file = $this->offlineFile($userId);
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file) ?: '[]';
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private function offlineFile(int $userId): string
    {
        return rtrim($this->offlineDir, '/\\') . '/loans_' . ($userId ?: 'guest') . '.json';
    }
}
