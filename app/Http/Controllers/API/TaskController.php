<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use OpenApi\Annotations as OA;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="To-Do List API",
 *     description="Laravel asosida qurilgan oddiy To-Do List REST API"
 * )
 */
class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Barcha tasklarni qaytaradi",
     *     tags={"Tasks"},
     *     @OA\Response(
     *         response=200,
     *         description="Tasklar ro‘yxati"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Task::all());
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Yangi task yaratadi",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Kitob o‘qish"),
     *             @OA\Property(property="description", type="string", example="O‘zbek adabiyoti haqida"),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Task yaratildi"),
     *     @OA\Response(response=422, description="Validatsiya xatosi")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,done',
        ]);

        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     summary="ID bo‘yicha bitta taskni qaytaradi",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID raqami",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Task topildi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Taskni yangilaydi",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Yangilanadigan task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Yangilangan sarlavha"),
     *             @OA\Property(property="description", type="string", example="Yangilangan matn"),
     *             @OA\Property(property="status", type="string", example="done")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Task yangilandi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,done',
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Taskni o‘chiradi",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="O‘chiriladigan task ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Task o‘chirildi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
