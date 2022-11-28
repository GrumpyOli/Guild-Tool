<?php

namespace App\Models\Wow;

use App\Models\wow\Guild;
use App\Models\wow\playableClass;
use App\Models\wow\playableRace;
use App\Models\wow\Realm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Character extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'characters';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    // Relation with realm database
    public function realm(){
        return $this->belongsTo( Realm::class );
    }

    public function race(){
        return $this->belongsTo( playableRace::class, 'playable_race_id');
    }

    public function class(){
        return $this->belongsTo( playableClass::class, 'playable_class_id');
    }

    public function guild(){
        return $this->belongsTo( Guild::class );
    }


    public static function findByNameAndRealm( $name, $realm, $findOrFail = true ){

        $Data = DB::table('characters')
                    ->select('characters.id')
                    ->join('realms', 'realms.id', '=', 'characters.realm_id')
                    ->where('characters.name', $name)
                    ->where('realms.name', $realm)
                    ->first(['characters.id']);

        // App::abort(404);
        
        if ( $Data == Null && $findOrFail == false ){
            return Null;
        }

        if ( $findOrFail ){
            return self::findOrFail( $Data->id );
        }

        return self::find( $Data->id );
    }

}
