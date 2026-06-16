<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entreprises';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_entreprise',
        'adresse_ligne1',
        'adresse_ligne2',
        'ville',
        'code_postal',
        'pays',
        'telephone_principal',
        'email_contact',
    ];
}