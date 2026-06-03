<?php

namespace Repositories;

use DataAccess\DbContext;
use Models\Transaction;
use Repositories\Interfaces\ITransactionRepository;
use PDO;

class TransactionRepository implements ITransactionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DbContext::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM transactions");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }

    public function findById(int $id): ?Transaction
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        );
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE user_id = :user_id ORDER BY date DESC");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }

    public function create(Transaction $transaction): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (user_id, source_id, category_id, amount, type, comment, date)
             VALUES (:user_id, :source_id, :category_id, :amount, :type, :comment, :date)"
        );
        $stmt->execute([
            'user_id'     => $transaction->user_id,
            'source_id'   => $transaction->source_id,
            'category_id' => $transaction->category_id,
            'amount'      => $transaction->amount,
            'type'        => $transaction->type,
            'comment'     => $transaction->comment,
            'date'        => $transaction->date,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(Transaction $transaction): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE transactions
             SET user_id = :user_id, source_id = :source_id, category_id = :category_id,
                 amount = :amount, type = :type, comment = :comment, date = :date
             WHERE id = :id"
        );

        return $stmt->execute([
            'user_id'     => $transaction->user_id,
            'source_id'   => $transaction->source_id,
            'category_id' => $transaction->category_id,
            'amount'      => $transaction->amount,
            'type'        => $transaction->type,
            'comment'     => $transaction->comment,
            'date'        => $transaction->date,
            'id'          => $transaction->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getPaginatedByUserId(int $userId, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE user_id = :user_id ORDER BY date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }

    public function getPaginatedAll(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions ORDER BY date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }

    public function getFilteredPaginatedByUserId(int $userId, int $limit, int $offset, ?string $categoryName = null, ?string $type = null, ?string $date = null): array
    {
        $sql = "SELECT t.* FROM transactions t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.user_id = :user_id";
        $params = [':user_id' => [$userId, PDO::PARAM_INT]];
        
        if ($categoryName) {
            $sql .= " AND c.name = :category_name";
            $params[':category_name'] = [$categoryName, PDO::PARAM_STR];
        }
        if ($type) {
            $sql .= " AND t.type = :type";
            $params[':type'] = [$type, PDO::PARAM_STR];
        }
        if ($date) {
            $sql .= " AND t.date = :date";
            $params[':date'] = [$date, PDO::PARAM_STR];
        }
        
        $sql .= " ORDER BY t.date DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = [$limit, PDO::PARAM_INT];
        $params[':offset'] = [$offset, PDO::PARAM_INT];

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value[0], $value[1]);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }

    public function getFilteredPaginatedAll(int $limit, int $offset, ?string $categoryName = null, ?string $type = null, ?string $date = null): array
    {
        $sql = "SELECT t.* FROM transactions t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE 1=1";
        $params = [];
        
        if ($categoryName) {
            $sql .= " AND c.name = :category_name";
            $params[':category_name'] = [$categoryName, PDO::PARAM_STR];
        }
        if ($type) {
            $sql .= " AND t.type = :type";
            $params[':type'] = [$type, PDO::PARAM_STR];
        }
        if ($date) {
            $sql .= " AND t.date = :date";
            $params[':date'] = [$date, PDO::PARAM_STR];
        }
        
        $sql .= " ORDER BY t.date DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = [$limit, PDO::PARAM_INT];
        $params[':offset'] = [$offset, PDO::PARAM_INT];

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value[0], $value[1]);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Transaction(
            $row['user_id'],
            $row['source_id'],
            $row['category_id'],
            $row['amount'],
            $row['type'],
            $row['comment'],
            $row['date'],
            $row['id']
        ), $rows);
    }
}
