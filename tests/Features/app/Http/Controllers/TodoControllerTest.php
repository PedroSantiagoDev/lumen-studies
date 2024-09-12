<?php

namespace Features\app\Http\Controllers;

use App\Models\Todo;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanListTodos()
    {
        // Prepare
        Todo::factory(5)->create();
        // Act
        $response = $this->get('/todos');
        // Assert
        $response->assertResponseOk();
        $response->seeJsonStructure(['current_page']);
    }

    public function testUserCanCreateTodo()
    {
        // Prepare
        $payload = [
            'title' => 'Tirar o lixo',
            'description' => 'Não esquece de tirar o lixo amanhã as 11:00 AM'
        ];
        // Act
        $result = $this->post('/todos', $payload);
        // Assert
        $result->assertResponseStatus(201);
        $result->seeInDatabase('todos', $payload);
    }

    public function testUserShouldSendTitleAndDescription()
    {
        // Prepare
        $payload = [
            'vários' => 'nada'
        ];
        // Act
        $response = $this->post('/todos', $payload);
        // Assert
        $response->assertResponseStatus(422);
    }

    public function testUserCanRetrieveASpecificTodo()
    {
        // Prepare
        $todo = Todo::factory()->create();
        // Act
        $uri = '/todos/' . $todo->id;
        $response = $this->get($uri);
        // Assert
        $response->assertResponseOk();
        $response->seeJsonContains([
            'title' => $todo->title
        ]);
    }

    public function testUserShouldReceive404WhenSearchSomethingThatDoesntExists()
    {
        // Prepare
        // Act
        $response = $this->get('/todos/1');
        // Assert
        $response->assertResponseStatus(404);
        $response->seeJsonContains(['error' => 'not found']);
    }

    public function testUserCanDeleteATodo()
    {
        // Prepare
        $model = Todo::factory()->create();
        // Act
        $response = $this->delete('/todos/' . $model->id);
        // Assert
        $response->assertResponseStatus(204); // 204 => No content
        $response->notSeeInDatabase('todos', [
            'id' => $model->id
        ]);
    }

    public function testUserCanSetTodoDone()
    {
        // Prepare
        $model = Todo::factory()->create();
        // Act
        $response = $this->patch('/todos/' . $model->id . '/status/done');
        // Assert
        $response->assertResponseStatus(200);
        $this->seeInDatabase('todos', [
           'id' => $model->id,
           'done' => true
        ]);
    }

    public function testUserCanSetTodoUndone()
    {
        // Prepare
        $model = Todo::factory()->create();
        // Act
        $response = $this->patch('/todos/' . $model->id . '/status/undone');
        // Assert
        $response->assertResponseStatus(200);
        $this->seeInDatabase('todos', [
            'id' => $model->id,
            'done' => false
        ]);
    }

    public function testUserCanEditTitleATodo()
    {
        // Prepare
        $model = Todo::factory()->create();
        $payload = [
            'title' => 'Remove o lixo',
        ];
        // Act
        $response = $this->put('/todos/' . $model->id, $payload);
        // Asssert
        $response->assertResponseOk();
        $response->seeJsonContains([
            'id' => $model->id,
            'title' => 'Remove o lixo',
        ]);
    }

    public function testUserCanEditDescriptionATodo()
    {
        // Prepare
        $model = Todo::factory()->create();
        $payload = [
            'description' => 'Remove o lixo da casa',
        ];
        // Act
        $response = $this->put('/todos/' . $model->id, $payload);
        // Asssert
        $response->assertResponseOk();
        $response->seeJsonContains([
            'id' => $model->id,
            'description' => 'Remove o lixo da casa',
        ]);
    }

    public function testUserSendsRequestWithoutTitleOrDescriptionAll()
    {
        // Prepare
        $model = Todo::factory()->create();
        $payload = [
            'qualquercoisa' => 'Remove o lixo da casa',
        ];
        // Act
        $response = $this->put('/todos/' . $model->id, $payload);
        // Asssert
        $response->assertResponseStatus(422);
    }
}
