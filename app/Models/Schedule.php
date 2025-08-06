<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property int $class_id
 * @property int $subject_id
 * @property int $teacher_id
 * @property string $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property string|null $room
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\SchoolClass $schoolClass
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\User $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule active()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule today()
 * @method static \Database\Factories\ScheduleFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include today's schedules.
     */
    public function scopeToday($query)
    {
        $today = strtolower(now()->format('l'));
        return $query->where('day_of_week', $today)->where('is_active', true);
    }

    /**
     * The class this schedule belongs to
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * The subject this schedule is for
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The teacher assigned to this schedule
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Attendance records for this schedule
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}