<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\File;

class Task extends Model
{
    use HasFactory, HasUuids;
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

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
    protected $fillable = ['title', 'description', 'folder_name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'folder_name', 'folder_name');
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';
}
