<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'image',
        'category_id',
        'author',
        'source',
    ];

    protected $casts = [
        'category_id' => 'integer',
    ];

    /**
     * A news article belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}