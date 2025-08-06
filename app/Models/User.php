<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $student_id
 * @property string|null $phone
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SchoolClass[] $classes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schedule[] $teachingSchedules
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LeaveRequest[] $leaveRequests
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User teachers()
 * @method static \Illuminate\Database\Eloquent\Builder|User students()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include teachers.
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    /**
     * Scope a query to only include students.
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Classes that this student belongs to
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
                    ->withPivot('enrolled_at', 'is_active')
                    ->wherePivot('is_active', true);
    }

    /**
     * Schedules that this teacher teaches
     */
    public function teachingSchedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Attendance records for this student
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Leave requests submitted by this student
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'student_id');
    }

    /**
     * Leave requests reviewed by this admin/teacher
     */
    public function reviewedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'reviewed_by');
    }
}