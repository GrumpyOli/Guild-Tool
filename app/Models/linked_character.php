<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linked_Character extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guild_id',
        'character_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'linked_characters';
    
}
