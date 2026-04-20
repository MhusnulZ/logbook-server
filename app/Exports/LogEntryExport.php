<?php

namespace App\Exports;

use App\Models\LogEntry;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class LogEntryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = LogEntry::query();

        if ($this->request->filled('search')) {
            $s = $this->request->search;
            $query->where(function($q) use ($s) {
                $q->where('visitor_name', 'like', "%{$s}%")
                  ->orWhere('vendor_name', 'like', "%{$s}%");
            });
        }

        if ($this->request->filled('purpose')) {
            $query->where('purpose', $this->request->purpose);
        }

        if ($this->request->filled('date')) {
            $query->whereDate('timestamp_in', $this->request->date);
        }

        return $query->orderBy('timestamp_in', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Pengunjung',
            'Instansi/Vendor',
            'Jumlah',
            'Tujuan',
            'Deskripsi',
            'Waktu Masuk',
            'Waktu Keluar',
            'Durasi',
            'Status',
        ];
    }

    public function map($log): array
    {
        static $no = 1;
        return [
            $no++,
            $log->timestamp_in ? \Carbon\Carbon::parse($log->timestamp_in)->format('Y-m-d') : '-',
            $log->visitor_name,
            $log->vendor_name,
            $log->quantity,
            $log->purpose,
            $log->description,
            $log->timestamp_in ? \Carbon\Carbon::parse($log->timestamp_in)->format('H:i') : '-',
            $log->timestamp_out ? \Carbon\Carbon::parse($log->timestamp_out)->format('H:i') : '-',
            $log->duration ?? '-',
            $log->status,
        ];
    }
}
