<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasUuids, HasFactory;
        /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
        /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    protected $fillable = ['name'];

    protected $guarded = ['id'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';
}
