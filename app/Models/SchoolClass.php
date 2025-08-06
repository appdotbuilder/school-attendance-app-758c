<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SchoolClass
 *
 * @property int $id
 * @property string $name
 * @property string $grade
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $students
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schedule[] $schedules
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass active()
 * @method static \Database\Factories\SchoolClassFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class SchoolClass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'grade',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Students enrolled in this class
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')
                    ->withPivot('enrolled_at', 'is_active')
                    ->wherePivot('is_active', true);
    }

    /**
     * Schedules for this class
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}