<?php

namespace Tests;

use App\Receita;
use PHPUnit\Framework\TestCase;

class ReceitaTest extends TestCase
{
    private function makeReceita(array $override = []): Receita
    {
        return new Receita(
            $override['id']           ?? 1,
            $override['nome']         ?? 'Brigadeiro',
            $override['descricao']    ?? 'Brigadeiro tradicional',
            $override['dataRegistro'] ?? '2026-01-01',
            $override['custo']        ?? 2.50,
            $override['tipoReceita']  ?? 'doce'
        );
    }

    // Teste 1
    public function testReceitaIsCreatedWithCorrectId(): void
    {
        $r = $this->makeReceita(['id' => 5]);
        $this->assertEquals(5, $r->getId());
    }

    // Teste 2
    public function testReceitaIsCreatedWithCorrectNome(): void
    {
        $r = $this->makeReceita(['nome' => 'Coxinha']);
        $this->assertEquals('Coxinha', $r->getNome());
    }

    // Teste 3
    public function testReceitaIsCreatedWithCorrectDescricao(): void
    {
        $r = $this->makeReceita(['descricao' => 'Coxinha crocante']);
        $this->assertEquals('Coxinha crocante', $r->getDescricao());
    }

    // Teste 4
    public function testReceitaIsCreatedWithCorrectDataRegistro(): void
    {
        $r = $this->makeReceita(['dataRegistro' => '2026-03-15']);
        $this->assertEquals('2026-03-15', $r->getDataRegistro());
    }

    // Teste 5
    public function testReceitaIsCreatedWithCorrectCusto(): void
    {
        $r = $this->makeReceita(['custo' => 4.50]);
        $this->assertEquals(4.50, $r->getCusto());
    }

    // Teste 6
    public function testReceitaIsCreatedWithCorrectTipo(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'salgada']);
        $this->assertEquals('salgada', $r->getTipoReceita());
    }

    // Teste 7
    public function testReceitaDoceIsDoce(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'doce']);
        $this->assertTrue($r->isDoce());
    }

    // Teste 8
    public function testReceitaDoceIsNotSalgada(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'doce']);
        $this->assertFalse($r->isSalgada());
    }

    // Teste 9
    public function testReceitaSalgadaIsSalgada(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'salgada']);
        $this->assertTrue($r->isSalgada());
    }

    // Teste 10
    public function testReceitaSalgadaIsNotDoce(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'salgada']);
        $this->assertFalse($r->isDoce());
    }

    // Teste 11
    public function testSetNomeUpdatesNome(): void
    {
        $r = $this->makeReceita();
        $r->setNome('Quindim');
        $this->assertEquals('Quindim', $r->getNome());
    }

    // Teste 12
    public function testSetNomeEmptyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $r = $this->makeReceita();
        $r->setNome('');
    }

    // Teste 13
    public function testSetNomeBlankThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $r = $this->makeReceita();
        $r->setNome('   ');
    }

    // Teste 14
    public function testSetCustoUpdatesCusto(): void
    {
        $r = $this->makeReceita();
        $r->setCusto(9.99);
        $this->assertEquals(9.99, $r->getCusto());
    }

    // Teste 15
    public function testSetCustoNegativeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $r = $this->makeReceita();
        $r->setCusto(-1.00);
    }

    // Teste 16
    public function testSetCustoZeroIsValid(): void
    {
        $r = $this->makeReceita();
        $r->setCusto(0.0);
        $this->assertEquals(0.0, $r->getCusto());
    }

    // Teste 17
    public function testSetTipoReceitaValid(): void
    {
        $r = $this->makeReceita(['tipoReceita' => 'doce']);
        $r->setTipoReceita('salgada');
        $this->assertEquals('salgada', $r->getTipoReceita());
    }

    // Teste 18
    public function testSetTipoReceitaInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $r = $this->makeReceita();
        $r->setTipoReceita('invalido');
    }

    // Teste 19
    public function testToArrayReturnsCorrectKeys(): void
    {
        $r = $this->makeReceita();
        $arr = $r->toArray();
        $this->assertArrayHasKey('id', $arr);
        $this->assertArrayHasKey('nome', $arr);
        $this->assertArrayHasKey('descricao', $arr);
        $this->assertArrayHasKey('dataRegistro', $arr);
        $this->assertArrayHasKey('custo', $arr);
        $this->assertArrayHasKey('tipoReceita', $arr);
    }

    // Teste 20
    public function testToArrayReturnsCorrectValues(): void
    {
        $r = $this->makeReceita(['id'=>3,'nome'=>'Pudim','custo'=>12.00,'tipoReceita'=>'doce']);
        $arr = $r->toArray();
        $this->assertEquals(3, $arr['id']);
        $this->assertEquals('Pudim', $arr['nome']);
        $this->assertEquals(12.00, $arr['custo']);
        $this->assertEquals('doce', $arr['tipoReceita']);
    }
}
