<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskEditRequest;
use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->has('paginate') ? $request->input('paginate') : 15;

        $tasks = Task::where('user_id', auth()->user()->id)
            ->where(function ($query) use ($request) {
                if ($request->has('title'))
                    $query->where('title', 'like', '%' . $request->input('title') . '%');

                if ($request->has('description'))
                    $query->where('description', 'like', '%' . $request->input('description') . '%');

            })
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', '=', 0);
            })
            ->paginate((int)$paginate);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TaskCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request)
    {

        $task = new Task;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->start_datetime = $request->start_datetime;
        $task->end_datetime = $request->end_datetime;
        $task->status = 0;
        $task->user_id = auth()->user()->id;

        $task->save();

        return back()->with('success', 'Task saved');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TaskEditRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskEditRequest $request, $id)
    {
        $task = Task::where('user_id', auth()->user()->id)
            ->findOrFail($id);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->start_datetime = $request->start_datetime;
        $task->end_datetime = $request->end_datetime;
        $task->status = $request->has('status');
        $task->save();


        return back()->with('success', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);
        $task->delete();

        return back()->with('success', 'Task was deleted successfully');
    }
}
