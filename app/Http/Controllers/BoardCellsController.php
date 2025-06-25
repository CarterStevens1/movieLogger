<?php

namespace App\Http\Controllers;

use App\Models\BoardCells;
use App\Models\BoardRows;
use Illuminate\Http\Request;

class BoardCellsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'board_row_id' => 'required|exists:board_rows,id',
            'board_column_id' => 'required|exists:board_columns,id',
            'value' => 'nullable|string',
            'tag_config' => 'nullable|array'
        ]);

        $cell = BoardCells::updateOrCreate(
            [
                'board_row_id' => $request->board_row_id,
                'board_column_id' => $request->board_column_id,
            ],
            [
                'board_id' => $request->board_id,
                'value' => $request->value,
                'tag_config' => $request->tag_config
            ]
        );

        return response()->json(['cell' => $cell]);
    }

    public function update(Request $request, BoardCells $cell)
    {
        $request->validate([
            'value' => 'nullable|string',
            'tag_config' => 'nullable|array'
        ]);

        $cell->update($request->only(['value', 'tag_config']));

        return response()->json(['cell' => $cell]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'cells' => 'required|array',
            'cells.*.board_row_id' => 'required|exists:board_rows,id',
            'cells.*.board_column_id' => 'required|exists:board_columns,id',
            'cells.*.value' => 'nullable|string',
            'cells.*.tag_config' => 'nullable|array'
        ]);

        $cells = [];
        foreach ($request->cells as $cellData) {
            $cells[] = BoardCells::updateOrCreate(
                [
                    'board_row_id' => $cellData['board_row_id'],
                    'board_column_id' => $cellData['board_column_id'],
                ],
                [
                    'board_id' => $request->board_id,
                    'value' => $cellData['value'] ?? null,
                    'tag_config' => $cellData['tag_config'] ?? null
                ]
            );
        }

        return response()->json(['cells' => $cells]);
    }

    public function reorder(Request $request)
    {
        $updates = $request->input('updates');

        foreach ($updates as $update) {
            // Update your row model with new position
            BoardRows::where('id', $update['rowId'])
                ->update(['position' => $update['newPosition']]);
        }

        return response()->json(['success' => true]);
    }
}
