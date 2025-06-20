<?php

namespace App\Http\Middleware;

use App\Models\Board;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkUserID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userID = Auth::user()->id;

        $boardID = Board::find($request->board);

        if (!$boardID) {
            return redirect()->route('boards')->with('error', 'Board not found.');
        }

        // Check if user is owner or shared
        $isOwner = $boardID->user_id === $userID;
        $isShared = $boardID->sharedUsers()->where('user_id', $userID)->exists();

        if (!$isOwner && !$isShared) {
            return redirect()->route('boards')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
