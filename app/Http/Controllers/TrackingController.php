<?php

namespace App\Http\Controllers;

use App\Models\tracked_character;
use App\Models\Wow\Character;
use App\Models\wow\Guild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    // To be deleted
    function change( Request $request, Character $character ){

        $Found = DB::table('tracked_characters')
                            ->where('guild_id', $character->guild->id )
                            ->where('character_id', $character->id )
                            ->first();
        
        var_dump( $Found );
        // Adding it
        if ( !$Found ){

            var_dump('Adding');
            DB::table('tracked_characters')->insert([
                [
                    'guild_id' => $character->guild->id,
                    'character_id' =>$character->id
                    ]
                ]);

            return redirect()->back()->with('message', 'Added!');
        }

        var_dump('Removing');
        // Deleting it

        DB::table('tracked_characters')
        ->where('guild_id', $character->guild->id )
        ->where('character_id', $character->id )
        ->delete();
        
        return redirect()->back()->with('message', 'Removed!');
    }

    function retrieve(Request $request, $guild_id, $character_id){

        return tracked_character::where('guild_id', $guild_id)->where('character_id', $character_id)->firstOrFail();
        
    }

    function store(Request $request){

        // Retrieving data and guild
        $validatedData = $request->validate([
            'character_id' => ['required'],
            'guild_id' => ['required']
        ]);

        $Guild = Guild::session_retrieve();

        // Creating a model
        $model = new tracked_character;
        $model->character_id = $validatedData['character_id'];
        $model->guild_id = $validatedData['guild_id'];
        $model->save();

        return true;

    }

    function delete(Request $request, $guild_id, $character_id){
        tracked_character::where('guild_id', $guild_id)->where('character_id', $character_id)->delete();
        return true;
    }
    
}
