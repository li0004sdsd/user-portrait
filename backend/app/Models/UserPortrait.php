<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPortrait extends Model {
    use HasFactory;
    protected $fillable = [
        'user_id','name','avatar','age','gender','occupation',
        'location','income_level','interests','pain_points','goals','status'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function tags() {
        return $this->belongsToMany(Tag::class, 'portrait_tags');
    }
}
