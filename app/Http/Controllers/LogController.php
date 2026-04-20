<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\Http\Request;
use App\Exports\LogEntryExport;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends Controller
{
    /**
     * Export logs to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new LogEntryExport($request), 'logbook_export_' . now()->format('Y-m-d') . '.xlsx');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LogEntry::query();

        // Filtering for History
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('visitor_name', 'like', "%{$s}%")
                  ->orWhere('vendor_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('date')) {
            $query->whereDate('timestamp_in', $request->date);
        }

        // Fetch paginated logs for History
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) { $perPage = 10; }
        
        $logs = $query->orderBy('timestamp_in', 'desc')->paginate($perPage)->withQueryString();
        
        // Fetch separate logs for Dashboard (Limited to 10 most recent)
        $dashboardLogs = LogEntry::latest('timestamp_in')->take(10)->get();
        
        $stats = LogEntry::getStats();

        return view('logbook', compact('logs', 'dashboardLogs', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'vendor_name' => 'required|string|max:255',
            'purpose' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
            'manual_date' => 'nullable|date',
            'manual_time_in' => 'nullable|string',
            'manual_time_out' => 'nullable|string',
        ]);

        $entryData = [
            'visitor_name' => $validated['visitor_name'],
            'vendor_name' => $validated['vendor_name'],
            'purpose' => $validated['purpose'],
            'quantity' => $validated['quantity'],
            'description' => $validated['description'],
            'status' => 'INSIDE',
        ];

        // Handle timestamps
        if ($request->filled('manual_date') && $request->filled('manual_time_in')) {
            $entryData['timestamp_in'] = $validated['manual_date'] . ' ' . $validated['manual_time_in'];
            
            if ($request->filled('manual_time_out')) {
                $entryData['timestamp_out'] = $validated['manual_date'] . ' ' . $validated['manual_time_out'];
                $entryData['status'] = 'OUT';
            }
        } else {
            $entryData['timestamp_in'] = now();
        }

        LogEntry::create($entryData);

        return redirect()->route('logs.index')->with('success', 'Log berhasil disimpan.');
    }

    /**
     * Checkout status update.
     */
    public function checkout($id)
    {
        $log = LogEntry::findOrFail($id);
        $log->update([
            'status' => 'OUT',
            'timestamp_out' => now(),
        ]);

        return redirect()->route('logs.index')->with('success', 'Berhasil checkout.');
    }
}
