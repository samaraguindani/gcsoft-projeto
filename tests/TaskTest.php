<?php

namespace Tests;

use App\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    // Teste 1
    public function testTaskIsCreatedWithCorrectId(): void
    {
        $task = new Task(1, 'Minha tarefa');
        $this->assertEquals(1, $task->getId());
    }

    // Teste 2
    public function testTaskIsCreatedWithCorrectTitle(): void
    {
        $task = new Task(1, 'Estudar GCSoft');
        $this->assertEquals('Estudar GCSoft', $task->getTitle());
    }

    // Teste 3
    public function testTaskDefaultStatusIsPending(): void
    {
        $task = new Task(1, 'Nova tarefa');
        $this->assertEquals('pending', $task->getStatus());
    }

    // Teste 4
    public function testTaskIsPendingByDefault(): void
    {
        $task = new Task(1, 'Nova tarefa');
        $this->assertTrue($task->isPending());
    }

    // Teste 5
    public function testTaskCanBeCompleted(): void
    {
        $task = new Task(1, 'Fazer testes');
        $task->complete();
        $this->assertTrue($task->isCompleted());
    }

    // Teste 6
    public function testCompletedTaskStatusIsCorrect(): void
    {
        $task = new Task(1, 'Fazer deploy');
        $task->complete();
        $this->assertEquals('completed', $task->getStatus());
    }

    // Teste 7
    public function testTaskCanBeCancelled(): void
    {
        $task = new Task(1, 'Tarefa cancelada');
        $task->cancel();
        $this->assertTrue($task->isCancelled());
    }

    // Teste 8
    public function testCancelledTaskStatusIsCorrect(): void
    {
        $task = new Task(1, 'Tarefa obsoleta');
        $task->cancel();
        $this->assertEquals('cancelled', $task->getStatus());
    }

    // Teste 9
    public function testCancelledTaskCanBeReopened(): void
    {
        $task = new Task(1, 'Tarefa reaberta');
        $task->cancel();
        $task->reopen();
        $this->assertTrue($task->isPending());
    }

    // Teste 10
    public function testReopenNonCancelledTaskThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $task = new Task(1, 'Tarefa pendente');
        $task->reopen();
    }

    // Teste 11
    public function testTaskTitleCanBeUpdated(): void
    {
        $task = new Task(1, 'Título antigo');
        $task->setTitle('Título novo');
        $this->assertEquals('Título novo', $task->getTitle());
    }

    // Teste 12
    public function testEmptyTitleThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $task = new Task(1, 'Título válido');
        $task->setTitle('');
    }

    // Teste 13
    public function testBlankTitleThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $task = new Task(1, 'Título válido');
        $task->setTitle('   ');
    }

    // Teste 14
    public function testTaskDescriptionIsStoredCorrectly(): void
    {
        $task = new Task(1, 'Tarefa com desc', 'Descrição detalhada');
        $this->assertEquals('Descrição detalhada', $task->getDescription());
    }

    // Teste 15
    public function testTaskDescriptionCanBeUpdated(): void
    {
        $task = new Task(1, 'Tarefa');
        $task->setDescription('Nova descrição');
        $this->assertEquals('Nova descrição', $task->getDescription());
    }

    // Teste 16
    public function testTaskDefaultDescriptionIsEmpty(): void
    {
        $task = new Task(1, 'Sem descrição');
        $this->assertEquals('', $task->getDescription());
    }

    // Teste 17
    public function testToArrayReturnsCorrectKeys(): void
    {
        $task = new Task(1, 'Tarefa array');
        $arr = $task->toArray();
        $this->assertArrayHasKey('id', $arr);
        $this->assertArrayHasKey('title', $arr);
        $this->assertArrayHasKey('description', $arr);
        $this->assertArrayHasKey('status', $arr);
    }

    // Teste 18
    public function testToArrayReturnsCorrectValues(): void
    {
        $task = new Task(42, 'Array test', 'Desc', 'pending');
        $arr = $task->toArray();
        $this->assertEquals(42, $arr['id']);
        $this->assertEquals('Array test', $arr['title']);
        $this->assertEquals('Desc', $arr['description']);
        $this->assertEquals('pending', $arr['status']);
    }

    // Teste 19
    public function testIsCompletedReturnsFalseWhenPending(): void
    {
        $task = new Task(1, 'Pendente');
        $this->assertFalse($task->isCompleted());
    }

    // Teste 20
    public function testIsCancelledReturnsFalseWhenCompleted(): void
    {
        $task = new Task(1, 'Concluída');
        $task->complete();
        $this->assertFalse($task->isCancelled());
    }
}
