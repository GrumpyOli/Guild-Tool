<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tracked_character extends Model
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
    protected $table = 'tracked_characters';

}
