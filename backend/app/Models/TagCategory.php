<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model {
    protected $fillable = ['user_id','name','color','description'];
    public function tags() { return $this->hasMany(Tag::class); }
    public function user() { return $this->belongsTo(User::class); }
}
