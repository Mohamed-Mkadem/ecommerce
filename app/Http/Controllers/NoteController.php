<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function newNote($id, $type)
    {
        return Inertia::render('Admin/Notes/Create', [
            'id' => $id,
            'type' => $type,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        $validated = $request->validated();
        $type = null;
        // dd($validated['id']);
        switch ($validated['type']) {
            case 'order':
                $type = "App\Models\Order";
                break;
            case 'client':
                $type = "App\Models\Client";
                break;
            default:
        }
        Note::create([
            'content' => $validated['content'],
            'notable_id' => $validated['id'],
            'notable_type' => $type,
            'user_id' => $request->user()->id
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)

    {
        return Inertia::render('Admin/Notes/Edit', ['note' =>$note]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $validated = $request->validated();
        $note->update($validated);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->back();
    }
}
