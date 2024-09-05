<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    public function getTodos(): JsonResponse
    {
        $todos = Todo::paginate();
        return response()->json($todos);
    }
    public function postTodo(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);

        $model = Todo::create($request->all());

        return response()->json($model, 201);
    }

    public function getTodo(int $todo): JsonResponse
    {
        $todo = Todo::find($todo);

        if (!$todo) {
            return response()->json(['error' => 'not found'], 404);
        }

        return response()->json($todo);
    }

    public function deleteTodo(Request $request, int $todo): JsonResponse
    {
        $todo = Todo::find($todo);

        if (!$todo) {
            return response()->json(['error' => 'not found'], 404);
        }

        $todo->delete();

        return response()->json([], 204);
    }

    public function putTodo(Request $request, int $todo): JsonResponse
    {
        $data = $request->only(['title', 'description']);

        if (empty($data)) {
            abort(422, 'No data provided.');
        }

        $todo = Todo::find($todo);

        if (!$todo) {
            return response()->json(['error' => 'not found'], 404);
        }

        $todo->update([
            'title' => $request->input('title', $todo->title),
            'description' => $request->input('description', $todo->description)
        ]);

        return response()->json($todo, 200);
    }

    public function patchTodoStatus(Request $request, int $todo, string $status): JsonResponse
    {
        if (!$this->validateAvailableStatus($status)) {
            return response()->json(['error' => 'avaible status: done, undone'], 400);
        }
        $todo = Todo::find($todo);

        if (!$todo) {
            return response()->json(['error' => 'not found'], 404);
        }

        $status === 'done'
            ? $todo->done()
            : $todo->undone();

        return response()->json($todo);
    }

    private function validateAvailableStatus(string $status): bool
    {
        return in_array($status, ['done', 'undone']);
    }
}
