<?php

namespace App\Models\wow;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guilds';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /*
    |
    | Static Function
    |
    |
    */

    static public function session_retrieve(): ?Guild {
        // return $_SESSION['BattleNet']['Token'][$index] ?? false;
        return session('currentGuild');
    }

    static public function session_flush(): void{
        session(['currentGuild' => Null]);
    }

    static public function session_register( Guild $Guild ){
        session(['currentGuild' => $Guild]);
    }

}
