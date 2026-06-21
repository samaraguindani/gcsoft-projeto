<?php

namespace App;

class Receita
{
    private int $id;
    private string $nome;
    private string $descricao;
    private string $dataRegistro;
    private float $custo;
    private string $tipoReceita;

    public function __construct(int $id, string $nome, string $descricao, string $dataRegistro, float $custo, string $tipoReceita)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->dataRegistro = $dataRegistro;
        $this->custo = $custo;
        $this->tipoReceita = $tipoReceita;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): string { return $this->descricao; }
    public function getDataRegistro(): string { return $this->dataRegistro; }
    public function getCusto(): float { return $this->custo; }
    public function getTipoReceita(): string { return $this->tipoReceita; }

    public function setNome(string $nome): void
    {
        if (empty(trim($nome))) {
            throw new \InvalidArgumentException('Nome não pode ser vazio.');
        }
        $this->nome = $nome;
    }

    public function setCusto(float $custo): void
    {
        if ($custo < 0) {
            throw new \InvalidArgumentException('Custo não pode ser negativo.');
        }
        $this->custo = $custo;
    }

    public function setTipoReceita(string $tipo): void
    {
        if (!in_array($tipo, ['doce', 'salgada'])) {
            throw new \InvalidArgumentException('Tipo deve ser doce ou salgada.');
        }
        $this->tipoReceita = $tipo;
    }

    public function isDoce(): bool { return $this->tipoReceita === 'doce'; }
    public function isSalgada(): bool { return $this->tipoReceita === 'salgada'; }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'nome'         => $this->nome,
            'descricao'    => $this->descricao,
            'dataRegistro' => $this->dataRegistro,
            'custo'        => $this->custo,
            'tipoReceita'  => $this->tipoReceita,
        ];
    }
}
