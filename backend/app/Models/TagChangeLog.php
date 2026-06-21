<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagChangeLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['tag_id', 'user_id', 'field', 'old_value', 'new_value', 'changed_at'];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function tag() { return $this->belongsTo(Tag::class); }
    public function user() { return $this->belongsTo(User::class); }
}
