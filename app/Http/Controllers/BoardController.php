<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Asset;
use App\Models\BoardColumn;
use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::with(['asset', 'employee', 'creator'])
            ->ordered()
            ->get()
            ->groupBy('status');

        return view('board.index', [
            'boardColumns' => BoardColumn::ordered()->get(),
            'tickets'      => $tickets,
            'assets'       => Asset::orderBy('asset_tag')->get(['id', 'asset_tag']),
            'employees'    => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()->id;
        $data['position'] = (int) Ticket::where('status', $data['status'])->max('position') + 1;

        Ticket::create($data);

        return redirect()->route('board.index')->with('success', 'Note added to the board.');
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->validated());

        return redirect()->route('board.index')->with('success', 'Note updated.');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        return redirect()->route('board.index')->with('success', 'Note deleted.');
    }

    /** Persist a drag-and-drop: move a card to a column and re-order that column. */
    public function move(Request $request)
    {
        abort_unless($request->user()->can(Permission::UpdateTickets->value), 403);

        $data = $request->validate([
            'id'     => ['required', 'integer', 'exists:tickets,id'],
            'status' => ['required', 'exists:board_columns,key'],
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer', 'exists:tickets,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['ids'] as $index => $id) {
                Ticket::whereKey($id)->update(['status' => $data['status'], 'position' => $index]);
            }
        });

        return response()->json(['ok' => true]);
    }

    /** Add a new status column to the board. */
    public function storeColumn(Request $request)
    {
        abort_unless($request->user()->can(Permission::CreateTickets->value), 403);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'in:gray,yellow,blue,green,pink,purple,red'],
        ]);

        // Build a unique slug key from the name.
        $key = Str::slug($data['name'], '_') ?: 'col_'.Str::lower(Str::random(5));
        $base = $key;
        $i = 1;
        while (BoardColumn::where('key', $key)->exists()) {
            $key = $base.'_'.$i++;
        }

        BoardColumn::create([
            'name'     => $data['name'],
            'key'      => $key,
            'color'    => $data['color'] ?? 'gray',
            'position' => (int) BoardColumn::max('position') + 1,
        ]);

        return redirect()->route('board.index')->with('success', 'Column added.');
    }

    /** Remove a status column (only when it is empty and not the last one). */
    public function destroyColumn(Request $request, BoardColumn $column)
    {
        abort_unless($request->user()->can(Permission::DeleteTickets->value), 403);

        if (BoardColumn::count() <= 1) {
            return back()->with('error', 'The board needs at least one column.');
        }
        if (Ticket::where('status', $column->key)->exists()) {
            return back()->with('error', 'Move or delete this column\'s notes before removing it.');
        }

        $column->delete();

        return redirect()->route('board.index')->with('success', 'Column removed.');
    }
}
