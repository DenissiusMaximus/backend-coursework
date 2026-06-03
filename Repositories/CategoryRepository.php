<?php

namespace Repositories;

use DataAccess\DbContext;
use Models\Category;
use Repositories\Interfaces\ICategoryRepository;
use PDO;

class CategoryRepository implements ICategoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DbContext::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => new Category(
            $row['name'],
            $row['id']
        ), $rows);
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new Category($row['name'], $row['id']);
    }

    public function create(Category $category): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categories (name) VALUES (:name)"
        );
        $stmt->execute([
            'name' => $category->name,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(Category $category): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE categories SET name = :name WHERE id = :id"
        );

        return $stmt->execute([
            'name' => $category->name,
            'id'   => $category->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
