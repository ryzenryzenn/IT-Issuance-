<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardController extends Controller
{
    /** Kanban columns, in order. */
    private const COLUMNS = [
        'todo'        => 'To Do',
        'in_progress' => 'In Progress',
        'done'        => 'Done',
    ];

    public function index()
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::with(['asset', 'employee', 'creator'])
            ->ordered()
            ->get()
            ->groupBy('status');

        return view('board.index', [
            'columns'   => self::COLUMNS,
            'tickets'   => $tickets,
            'assets'    => Asset::orderBy('asset_tag')->get(['id', 'asset_tag']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()->id;
        // Drop the new note at the bottom of its column.
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

    /**
     * Persist a drag-and-drop: move a card to a column and re-order that column.
     * Called via AJAX from SortableJS.
     */
    public function move(Request $request)
    {
        abort_unless($request->user()->can(Permission::UpdateTickets->value), 403);

        $data = $request->validate([
            'id'     => ['required', 'integer', 'exists:tickets,id'],
            'status' => ['required', Rule::in(Ticket::STATUSES)],
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer', 'exists:tickets,id'],
        ]);

        DB::transaction(function () use ($data) {
            Ticket::whereKey($data['id'])->update(['status' => $data['status']]);

            foreach ($data['ids'] as $index => $id) {
                Ticket::whereKey($id)->update(['status' => $data['status'], 'position' => $index]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
