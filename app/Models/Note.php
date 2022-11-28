<?php

namespace App\Models;

use App\Models\Blizzard\Account;
use App\Models\Wow\Character;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notes';

    public function account(){
        return $this->belongsTo( Account::class );
    }

    public function character(){
        return $this->belongsTo( Character::class );
    }

    public function guild(){
        return $this->belongsTo( Guild::class );
    }
}
