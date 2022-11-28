<?php

namespace App\Http\Controllers;

use App\Models\Linked_Character;
use Illuminate\Http\Request;

class LinkedCharactersController extends Controller
{
    //
    function delete(Request $request, $guild_id, $character_id){
        Linked_Character::where('guild_id', $guild_id)->where('character_id', $character_id)->delete();
        return true;
    }
}
