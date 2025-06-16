<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = Board::where('user_id', Auth::user()->id)->latest()->get();
        $user = Auth::user();
        $sharedBoards = $user->sharedBoards()->get();

        return view('boards', [
            'boards' => $boards,
        ], compact('sharedBoards'));
    }


    public function create()
    {
        return view('boards.create');
    }

    public function store(Request $request)
    {

        $request->merge(['user_id' => Auth::user()->id]);
        $boardAtrributes = $request->validate([
            'user_id' => ['required'],
            'name' => ['required'],
            'description' => ['required'],
            'tags' => ['nullable'],
        ]);

        $board = Board::create($boardAtrributes);

        return redirect()->route('boards.show', Board::find($board->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Check if user is the same as the user_id of the board
        $board = Board::find($id);
        return view('boards.show', compact('board'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $board = Board::find($id);
        // Return view with success message
        return view('boards.edit', compact('board'))->with('success', 'Board updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find board ID
        $request->validate([
            'name' => ['required'],
            'description' => ['required'],
            'tags' => ['nullable'],
        ]);
        $board = Board::find($id);

        $board->update([
            'name' => $request->name,
            'description' => $request->description,
            'tags' => $request->tags,
        ]);
        return redirect()->route('boards.show', $board);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $board = Board::find($id);
        $board->delete();
        return redirect()->route('boards');
    }

    public function share(Request $request, $boardId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', strtolower($request->email))->first();
        if (!$user) {
            return back()->with('error', 'User does not exist.');
        }

        $board = Board::findOrFail($boardId);
        if ($board->sharedUsers()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Board already shared with user.');
        }
        // Attach the user to the board (many-to-many) without duplicates
        $board->sharedUsers()->attach($user->id, [
            'board_owner_id' => $board->user_id,
        ]);

        // Return view with success message
        return  back()->with('success', 'Board shared with user successfully.');
    }

    public function unshare(string $id) {}
}
