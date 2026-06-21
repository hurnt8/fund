<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ContractTemplate;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'type', 'gender', 'created_by', 'invitation_token',
        'phone', 'address', 'birth_date', 'id_type', 'id_number', 'currency', 'locale', 'balance',
        'bank_account', 'bic',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date'        => 'date',
        'balance'           => 'decimal:2',
    ];

    // Demandes créées par cet admin
    public function createdLoans()
    {
        return $this->hasMany(LoanRequest::class, 'admin_id');
    }

    // Demandes dont cet utilisateur est le client
    public function clientLoans()
    {
        return $this->hasMany(LoanRequest::class, 'client_id');
    }

    // Modèles de contrats attribués à cet admin
    public function assignedTemplates()
    {
        return $this->belongsToMany(
            ContractTemplate::class,
            'admin_contract_template',
            'admin_id',
            'contract_template_id'
        );
    }
}
