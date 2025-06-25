<?php

namespace App\Http\Controllers;

use App\Models\BoardColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardColumnsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
        ]);

        $board = Auth::user()->boards()->findOrFail($validated['board_id']);
        $maxIndex = $board->columns()->max('column_index') ?? -1;
        $newIndex = $maxIndex + 1;

        $column = BoardColumns::create([
            'board_id' => $board->id,
            'column_index' => $newIndex,
            'label' => BoardColumns::generateLabel($newIndex),
            'position' => $newIndex
        ]);

        return response()->json([
            'success' => true,
            'column' => $column
        ]);
    }

    public function updateSort(Request $request, BoardColumns $column)
    {
        $validated = $request->validate([
            'sort_config' => 'nullable|array'
        ]);

        $column->update([
            'sort_config' => $validated['sort_config']
        ]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'columns' => 'required|array',
            'columns.*.id' => 'required|exists:board_columns,id',
            'columns.*.position' => 'required|integer'
        ]);

        $board = Auth::user()->boards()->findOrFail($validated['board_id']);

        foreach ($validated['columns'] as $columnData) {
            $board->columns()->where('id', $columnData['id'])
                ->update(['position' => $columnData['position']]);
        }

        return response()->json(['success' => true]);
    }
}
