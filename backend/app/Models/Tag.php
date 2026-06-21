<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model {
    use HasFactory;
    protected $fillable = ['tag_category_id','user_id','name','value','weight'];
    public function category() { return $this->belongsTo(TagCategory::class, 'tag_category_id'); }
    public function portraits() {
        return $this->belongsToMany(UserPortrait::class, 'portrait_tags');
    }
    public function user() { return $this->belongsTo(User::class); }
}
