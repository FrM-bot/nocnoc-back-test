<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Task;
class File extends Model
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
    protected $fillable = ['name', 'original_name', 'folder_name', 'original_name', 'directory', 'url', 'task_id'];

    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'folder_name', 'folder_name');
    }

    // php artisan make:migration add_campo_to_tabla_existente --table=nombre_de_la_tabla
    protected $guarded = ['id'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'files';
}
