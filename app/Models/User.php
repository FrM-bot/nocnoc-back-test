<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
class User extends Model
{
    use HasUuids, HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
        /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
        'role_id'
    ];

    protected $guarded = ['id'];

    public static $signUpRules = [
        'email' => ['required', 'email', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
    ];

    public static $logInRules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8'],
    ];
    
    public static $uniqueEmailRules = [
        'email' => ['required', 'email', 'unique:users']
    ];
    
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'role_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'password' => 'hashed',
    //     ];
    // }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
}
