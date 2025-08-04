<?php

namespace App\Http\Controllers;

use App\Models\BoardRows;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardRowsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
        ]);

        $board = Auth::user()->boards()->findOrFail($validated['board_id']);
        $maxIndex = $board->rows()->max('row_index') ?? 0;
        $newIndex = $maxIndex + 1;

        $row = BoardRows::create([
            'board_id' => $board->id,
            'row_index' => $newIndex,
            'label' => BoardRows::generateLabel($newIndex),
            'position' => $newIndex - 1 // 0-based position
        ]);

        return response()->json([
            'success' => true,
            'row' => $row
        ]);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:board_rows,id',
            'rows.*.position' => 'required|integer'
        ]);

        $board = Auth::user()->boards()->findOrFail($validated['board_id']);

        foreach ($validated['rows'] as $rowData) {
            $board->rows()->where('id', $rowData['id'])
                ->update(['position' => $rowData['position']]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(BoardRows $row)
    {
        $this->authorize('update', $row->board);

        $row->delete();

        return response()->json(['success' => true]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'count' => 'required|integer|min:1'
        ]);

        $rows = [];
        $maxRowIndex = BoardRows::where('board_id', $request->board_id)->max('row_index') ?? 0;

        for ($i = 0; $i < $request->count; $i++) {
            $newRow = new BoardRows();
            $newRow->board_id = $request->board_id;
            $newRow->row_index = $maxRowIndex + $i + 1;
            $newRow->label = $newRow->row_index;
            $newRow->save();
            $rows[] = $newRow;
        }

        return response()->json(['rows' => $rows]);
    }
}
