<?php

namespace App;

use PDO;

class ReceitaRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM receita ORDER BY data_registro DESC');
        return array_map(fn($r) => $this->hydrate($r), $stmt->fetchAll());
    }

    public function findById(int $id): ?Receita
    {
        $stmt = $this->pdo->prepare('SELECT * FROM receita WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findByTipo(string $tipo): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM receita WHERE tipo_receita = :tipo ORDER BY nome');
        $stmt->execute([':tipo' => $tipo]);
        return array_map(fn($r) => $this->hydrate($r), $stmt->fetchAll());
    }

    public function save(Receita $receita): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO receita (nome, descricao, data_registro, custo, tipo_receita)
             VALUES (:nome, :descricao, :data_registro, :custo, :tipo_receita)'
        );
        return $stmt->execute([
            ':nome'         => $receita->getNome(),
            ':descricao'    => $receita->getDescricao(),
            ':data_registro'=> $receita->getDataRegistro(),
            ':custo'        => $receita->getCusto(),
            ':tipo_receita' => $receita->getTipoReceita(),
        ]);
    }

    public function update(Receita $receita): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE receita SET nome=:nome, descricao=:descricao, data_registro=:data_registro,
             custo=:custo, tipo_receita=:tipo_receita WHERE id=:id'
        );
        return $stmt->execute([
            ':nome'         => $receita->getNome(),
            ':descricao'    => $receita->getDescricao(),
            ':data_registro'=> $receita->getDataRegistro(),
            ':custo'        => $receita->getCusto(),
            ':tipo_receita' => $receita->getTipoReceita(),
            ':id'           => $receita->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM receita WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM receita')->fetchColumn();
    }

    public function countByTipo(string $tipo): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM receita WHERE tipo_receita = :tipo');
        $stmt->execute([':tipo' => $tipo]);
        return (int) $stmt->fetchColumn();
    }

    private function hydrate(array $row): Receita
    {
        return new Receita(
            (int) $row['id'],
            $row['nome'],
            $row['descricao'],
            $row['data_registro'],
            (float) $row['custo'],
            $row['tipo_receita']
        );
    }
}
