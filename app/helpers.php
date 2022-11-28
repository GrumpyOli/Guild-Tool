<?php

use App\Models\wow\Guild;

function Guild(){
    return Guild::session_retrieve();
}

?>