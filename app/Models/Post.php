<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'image',
        'slug',
        'user_id',
        'published_at'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function claps(){
        return $this->hasMany(Clap::class);
    }

    public function readTime($wordsPerMinute = 100) {
        $wordcount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordcount / $wordsPerMinute);

        return max(1, $minutes);
    }

    public function imageUrl(){
        if($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getCreatedAt (){
        return $this->created_at->format('M d, Y');
    }
}
