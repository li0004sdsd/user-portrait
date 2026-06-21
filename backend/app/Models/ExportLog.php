<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'exported_at', 'row_count'];

    protected function casts(): array
    {
        return [
            'exported_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
}
