<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\wow\Guild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    //
    function store( Request $request ){

        $validatedData = $request->validate([
            'message' => ['required'],
            'character_id' => ['numeric']
        ]);

        $note = new Note(); 
        $note->guild_id = Guild::session_retrieve()->id;
        $note->account_id = Auth::user()->account->id;
        $note->message = $validatedData['message'];
        $note->character_id = $validatedData['character_id'];
        $note->save();

        return redirect()->route('guild.notes')->with('message', 'Note has been added with success');

    }
}
