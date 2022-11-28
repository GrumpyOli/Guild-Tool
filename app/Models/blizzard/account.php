<?php

namespace App\Models\Blizzard;

use App\Models\User;
use App\Models\Wow\Character;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'battle_tag',
        'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public function user(){
        return $this->hasOne( User::class );
    }

    public function isBelongsToGuild( int $guild_id ): bool {
        return true;
    }

    public function characters(){
        return $this->belongsToMany(
            Character::class,
            'account_character'
        );
    }

}
