<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Boost
    |--------------------------------------------------------------------------
    | true = active automatiquement le boost
    | false = désactive (uniquement boost payant)
    */
    'auto_boost' => env('BOOST_AUTO', false),

    /*
    |--------------------------------------------------------------------------
    | Durée du boost en heures
    |--------------------------------------------------------------------------
    | Exemple : 24 = boost valable pendant 24 heures
    */
    'duration_hours' => env('BOOST_DURATION_HOURS', 24),

];
