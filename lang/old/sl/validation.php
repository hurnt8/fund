<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Polje :attribute mora biti sprejeto.',
    'accepted_if' => 'Polje :attribute mora biti sprejeto, ko je :other :value.',
    'active_url' => 'Polje :attribute mora biti veljavna URL povezava.',
    'after' => 'Polje :attribute mora biti datum po :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum po ali enak :date.',
    'alpha' => 'Polje :attribute sme vsebovati le črke.',
    'alpha_dash' => 'Polje :attribute sme vsebovati le črke, številke, vezaje in podčrtaje.',
    'alpha_num' => 'Polje :attribute sme vsebovati le črke in številke.',
    'array' => 'Polje :attribute mora biti seznam.',
    'ascii' => 'Polje :attribute mora vsebovati le enobite alfanumerične znake in simbole.',
    'before' => 'Polje :attribute mora biti datum pred :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum pred ali enak :date.',
    'between' => [
        'array' => 'Polje :attribute mora imeti med :min in :max elementi.',
        'file' => 'Polje :attribute mora biti med :min in :max kilobajti.',
        'numeric' => 'Polje :attribute mora biti med :min in :max.',
        'string' => 'Polje :attribute mora imeti med :min in :max znaki.',
    ],
    'boolean' => 'Polje :attribute mora biti resnično ali neresnično.',
    'can' => 'Polje :attribute vsebuje nepooblaščeno vrednost.',
    'confirmed' => 'Potrditev polja :attribute se ne ujema.',
    'current_password' => 'Geslo je napačno.',
    'date' => 'Polje :attribute mora biti veljaven datum.',
    'date_equals' => 'Polje :attribute mora biti datum enak :date.',
    'date_format' => 'Polje :attribute mora ustrezati formatu :format.',
    'decimal' => 'Polje :attribute mora imeti :decimal decimalnih mest.',
    'declined' => 'Polje :attribute mora biti zavrnjeno.',
    'declined_if' => 'Polje :attribute mora biti zavrnjeno, ko je :other :value.',
    'different' => 'Polje :attribute in :other morata biti različna.',
    'digits' => 'Polje :attribute mora imeti :digits digitov.',
    'digits_between' => 'Polje :attribute mora imeti med :min in :max digitov.',
    'dimensions' => 'Polje :attribute ima neveljavne dimenzije slike.',
    'distinct' => 'Polje :attribute ima podvojen vrednost.',
    'doesnt_end_with' => 'Polje :attribute ne sme končati z enim od naslednjih: :values.',
    'doesnt_start_with' => 'Polje :attribute ne sme začeti z enim od naslednjih: :values.',
    'email' => 'Polje :attribute mora biti veljavna e-poštna naslov.',
    'ends_with' => 'Polje :attribute mora končati z enim od naslednjih: :values.',
    'enum' => 'Izbrana :attribute je neveljavna.',
    'exists' => 'Izbrana :attribute je neveljavna.',
    'extensions' => 'Polje :attribute mora imeti eno od naslednjih razširitev: :values.',
    'file' => 'Polje :attribute mora biti datoteka.',
    'filled' => 'Polje :attribute mora imeti vrednost.',
    'gt' => [
        'array' => 'Polje :attribute mora imeti več kot :value elementov.',
        'file' => 'Polje :attribute mora biti več kot :value kilobajtov.',
        'numeric' => 'Polje :attribute mora biti več kot :value.',
        'string' => 'Polje :attribute mora biti več kot :value znakov.',
    ],
    'gte' => [
        'array' => 'Polje :attribute mora imeti :value elementov ali več.',
        'file' => 'Polje :attribute mora biti večje od ali enako :value kilobajtom.',
        'numeric' => 'Polje :attribute mora biti večje od ali enako :value.',
        'string' => 'Polje :attribute mora biti večje od ali enako :value znakov.',
    ],
    'hex_color' => 'Polje :attribute mora biti veljavna hexadecimalna barva.',
    'image' => 'Polje :attribute mora biti slika.',
    'in' => 'Izbrana :attribute je neveljavna.',
    'in_array' => 'Polje :attribute mora obstajati v :other.',
    'integer' => 'Polje :attribute mora biti celo število.',
    'ip' => 'Polje :attribute mora biti veljavna IP naslov.',
    'ipv4' => 'Polje :attribute mora biti veljavna IPv4 naslov.',
    'ipv6' => 'Polje :attribute mora biti veljavna IPv6 naslov.',
    'json' => 'Polje :attribute mora biti veljavna JSON niz.',
    'lowercase' => 'Polje :attribute mora biti v majhnih črkah.',
    'lt' => [
        'array' => 'Polje :attribute mora imeti manj kot :value elementov.',
        'file' => 'Polje :attribute mora biti manj kot :value kilobajtov.',
        'numeric' => 'Polje :attribute mora biti manj kot :value.',
        'string' => 'Polje :attribute mora biti manj kot :value znakov.',
    ],
    'lte' => [
        'array' => 'Polje :attribute ne sme imeti več kot :value elementov.',
        'file' => 'Polje :attribute mora biti manjše ali enako :value kilobajtov.',
        'numeric' => 'Polje :attribute mora biti manjše ali enako :value.',
        'string' => 'Polje :attribute mora biti manjše ali enako :value znakov.',
    ],
    'mac_address' => 'Polje :attribute mora biti veljavna MAC naslov.',
    'max' => [
        'array' => 'Polje :attribute ne sme imeti več kot :max elementov.',
        'file' => 'Polje :attribute ne sme biti večje od :max kilobajtov.',
        'numeric' => 'Polje :attribute ne sme biti večje od :max.',
        'string' => 'Polje :attribute ne sme biti večje od :max znakov.',
    ],
    'max_digits' => 'Polje :attribute ne sme imeti več kot :max digitov.',
    'mimes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'min' => [
        'array' => 'Polje :attribute mora imeti vsaj :min elementov.',
        'file' => 'Polje :attribute mora biti vsaj :min kilobajtov.',
        'numeric' => 'Polje :attribute mora biti vsaj :min.',
        'string' => 'Polje :attribute mora biti vsaj :min znakov.',
    ],
    'min_digits' => 'Polje :attribute mora imeti vsaj :min digitov.',
    'missing' => 'Polje :attribute mora biti manjkajoče.',
    'missing_if' => 'Polje :attribute mora biti manjkajoče, ko je :other :value.',
    'missing_unless' => 'Polje :attribute mora biti manjkajoče, razen če je :other :value.',
    'missing_with' => 'Polje :attribute mora biti manjkajoče, ko je :values prisotna.',
    'missing_with_all' => 'Polje :attribute mora biti manjkajoče, ko so prisotne :values.',
    'multiple_of' => 'Polje :attribute mora biti večkratnik :value.',
    'not_in' => 'Izbrana :attribute je neveljavna.',
    'not_regex' => 'Oblika polja :attribute je neveljavna.',
    'numeric' => 'Polje :attribute mora biti številka.',
    'password' => [
        'letters' => 'Polje :attribute mora vsebovati vsaj eno črko.',
        'mixed' => 'Polje :attribute mora vsebovati vsaj eno veliko in eno malo črko.',
        'numbers' => 'Polje :attribute mora vsebovati vsaj eno številko.',
        'symbols' => 'Polje :attribute mora vsebovati vsaj en simbol.',
        'uncompromised' => 'Danes :attribute se je pojavilo v podatkovnem uhajanju. Izberite drugačen :attribute.',
    ],

    'present' => 'Polje :attribute mora biti prisotno.',
    'present_if' => 'Polje :attribute mora biti prisotno, ko je :other :value.',
    'present_unless' => 'Polje :attribute mora biti prisotno, razen če je :other :value.',
    'present_with' => 'Polje :attribute mora biti prisotno, ko je :values prisotna.',
    'present_with_all' => 'Polje :attribute mora biti prisotno, ko so prisotne :values.',
    'prohibited' => 'Polje :attribute je prepovedano.',
    'prohibited_if' => 'Polje :attribute je prepovedano, ko je :other :value.',
    'prohibited_unless' => 'Polje :attribute je prepovedano, razen če je :other v :values.',
    'prohibits' => 'Polje :attribute prepoveduje prisotnost :other.',
    'regex' => 'Oblika polja :attribute je neveljavna.',
    'required' => 'Polje :attribute je obvezno.',
    'required_array_keys' => 'Polje :attribute mora vsebovati vnose za: :values.',
    'required_if' => 'Polje :attribute je obvezno, ko je :other :value.',
    'required_if_accepted' => 'Polje :attribute je obvezno, ko je :other sprejeto.',
    'required_unless' => 'Polje :attribute je obvezno, razen če je :other v :values.',
    'required_with' => 'Polje :attribute je obvezno, ko je :values prisotna.',
    'required_with_all' => 'Polje :attribute je obvezno, ko so prisotne :values.',
    'required_without' => 'Polje :attribute je obvezno, ko :values ni prisotna.',
    'required_without_all' => 'Polje :attribute je obvezno, ko nobena od :values ni prisotna.',
    'same' => 'Polje :attribute mora ustrezati :other.',
    'size' => [
        'array' => 'Polje :attribute mora vsebovati :size elementov.',
        'file' => 'Polje :attribute mora biti :size kilobajtov.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string' => 'Polje :attribute mora imeti :size znakov.',
    ],
    'starts_with' => 'Polje :attribute mora začeti z enim od naslednjih: :values.',
    'string' => 'Polje :attribute mora biti niz.',
    'timezone' => 'Polje :attribute mora biti veljavna časovna cona.',
    'unique' => 'Polje :attribute je že zasedeno.',
    'uploaded' => 'Polje :attribute ni uspelo prenesti.',
    'uppercase' => 'Polje :attribute mora biti z velikimi črkami.',
    'url' => 'Polje :attribute mora biti veljavna URL povezava.',
    'ulid' => 'Polje :attribute mora biti veljaven ULID.',
    'uuid' => 'Polje :attribute mora biti veljaven UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
