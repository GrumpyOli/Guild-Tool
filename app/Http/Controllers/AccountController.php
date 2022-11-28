<?php

namespace App\Http\Controllers;

use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Models\Wow\Character;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    //

    function summary( Request $request ){
        return view('account');
    }

    function updateCharacters( Request $request ){

        $infos = APIRequest::getFirstJSON( Url::accountProfilSummary() );
        $user_account = Auth::user()->account;
        $account_id = Auth::user()->account->id;
        $upserts = [];

        if ( !$infos ){
            return redirect()->back()->withErrors('Request has fail');
        }

        if ( empty( $infos->wow_accounts ) ){
            return redirect()->back()->withErrors('We didn\'t find any world of warcraft account');
        }

        // Looping through every wow accounts
        foreach( $infos->wow_accounts as $account ){
            foreach( $account->characters as $character ){

                DB::table('account_character')->insertOrIgnore([
                    'account_id' => $account_id,
                    'character_id' => $character->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $upserts[] = [
                    'id' => $character->id,
                    'name' => $character->name,
                    'region' => Token::retrieve()->getRegion(),
                    'faction' => $character->faction->name,
                    'realm_id' => $character->realm->id,
                    'playable_race_id' => $character->playable_race->id,
                    'playable_class_id' => $character->playable_class->id,
                    'level' => $character->level,
                    'updated_at' => Carbon::now()
                ];
            }
        } 

        Character::upsert(
            $upserts,
            ['id', 'region'],
            ['name', 'rank','faction', 'realm_id', 'playable_race_id', 'level', 'updated_at']
        );

        $user_account->updated_at = Carbon::now();
        $user_account->save();

        return redirect()->back()->with('message', 'Account has been updated with success!');

    }
}
