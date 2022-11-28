<?php

namespace App\Models\wow;

use App\Blizzard\API\Url;
use App\Models\Note;
use App\Models\Wow\Character;
use App\Models\wow\guild\Rank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Return a completely built URL
     * @param mixed $param roster|
     * @return void 
     */
    public function makeURL( $param ){

        switch ( $param ){
            case 'roster' :
                var_dump( Url::guildInfos( $this->realmSlug, $this->name ) );
                break;
        }

    }

    public function realm(){
        return $this->belongsTo( Realm::class, 'realmSlug', 'slug');
    }

    public function members(){
        return $this->HasMany( Character::class );
    }

    public function tracked_characters(){
        return $this->belongsToMany( 
            Character::class,
            'tracked_characters',
            'guild_id',
            'character_id'
        );
    }

    public function linked_characters(){
        return $this->belongsToMany( 
            Character::class,
            'linked_characters',
            'guild_id',
            'character_id'
        );
    }

    public function ranks(){
        return $this->hasMany( Rank::class );
    }

    public function notes(){
        return $this->hasMany( Note::class );
    }
}
