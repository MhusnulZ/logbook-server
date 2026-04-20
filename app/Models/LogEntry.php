<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    protected $casts = [
        'timestamp_in' => 'datetime',
        'timestamp_out' => 'datetime',
    ];

    protected $fillable = [
        'visitor_name',
        'vendor_name',
        'purpose',
        'quantity',
        'description',
        'timestamp_in',
        'timestamp_out',
        'status',
    ];

    /**
     * Get the duration of the visit.
     */
    public function getDurationAttribute()
    {
        if (!$this->timestamp_in || !$this->timestamp_out) {
            return null;
        }

        $diff = $this->timestamp_in->diff($this->timestamp_out);

        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' jam';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . ' mnt';
        }

        if (empty($parts)) {
            return $diff->s . ' dtk';
        }

        return implode(' ', $parts);
    }

    /**
     * Get stats for the dashboard.
     */
    public static function getStats()
    {
        $today = now()->toDateString();
        
        return [
            'totalToday' => self::whereDate('timestamp_in', $today)->count(),
            'personnelInside' => self::where('status', 'INSIDE')->sum('quantity'),
        ];
    }
}
