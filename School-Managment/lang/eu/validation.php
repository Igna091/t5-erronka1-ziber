<?php

/*
| Validation messages in Basque, only for the rules the app uses.
| Anything missing falls back to Laravel's English messages.
*/

return [
    'after_or_equal' => ':attribute eremuak :date edo ondorengo data izan behar du.',
    'boolean' => ':attribute eremuak egia edo gezurra izan behar du.',
    'confirmed' => ':attribute eremuaren berrespena ez dator bat.',
    'current_password' => 'Pasahitza ez da zuzena.',
    'date' => ':attribute eremuak data baliozkoa izan behar du.',
    'different' => ':attribute eta :other eremuek desberdinak izan behar dute.',
    'email' => ':attribute eremuak email baliozkoa izan behar du.',
    'exists' => 'Hautatutako :attribute ez da baliozkoa.',
    'in' => 'Hautatutako :attribute ez da baliozkoa.',
    'integer' => ':attribute eremuak zenbaki osoa izan behar du.',
    'max' => [
        'numeric' => ':attribute eremua ezin da :max baino handiagoa izan.',
        'string' => ':attribute eremuak ezin ditu :max karaktere baino gehiago izan.',
    ],
    'min' => [
        'numeric' => ':attribute eremuak gutxienez :min izan behar du.',
        'string' => ':attribute eremuak gutxienez :min karaktere izan behar ditu.',
    ],
    'password' => [
        'letters' => ':attribute eremuak letra bat izan behar du gutxienez.',
        'mixed' => ':attribute eremuak letra larri bat eta xehe bat izan behar ditu gutxienez.',
        'numbers' => ':attribute eremuak zenbaki bat izan behar du gutxienez.',
        'symbols' => ':attribute eremuak ikur bat izan behar du gutxienez.',
        'uncompromised' => 'Adierazitako :attribute datu-filtrazio batean agertu da. Aukeratu beste bat.',
    ],
    'required' => ':attribute eremua derrigorrezkoa da.',
    'string' => ':attribute eremuak testua izan behar du.',
    'unique' => ':attribute hori dagoeneko erabiltzen ari da.',

    'attributes' => [
        'capacity' => 'plazak',
        'code' => 'kodea',
        'course_id' => 'ikastaroa',
        'current_password' => 'uneko pasahitza',
        'description' => 'deskribapena',
        'dni' => 'NANa',
        'duration_hours' => 'iraupena',
        'email' => 'emaila',
        'end_date' => 'amaiera-data',
        'locale' => 'hizkuntza',
        'name' => 'izena',
        'password' => 'pasahitza',
        'phone' => 'telefonoa',
        'search' => 'bilaketa',
        'start_date' => 'hasiera-data',
        'status' => 'egoera',
        'student_id' => 'ikaslea',
        'surname' => 'abizenak',
        'token' => 'esteka',
    ],
];
