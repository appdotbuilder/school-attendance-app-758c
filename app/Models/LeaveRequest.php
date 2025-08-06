<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LeaveRequest
 *
 * @property int $id
 * @property int $student_id
 * @property string $from_date
 * @property string $to_date
 * @property string $type
 * @property string $reason
 * @property string|null $evidence_file
 * @property string $status
 * @property string|null $admin_notes
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User $student
 * @property-read \App\Models\User|null $reviewer
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest whereFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest whereToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest pending()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest approved()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveRequest rejected()
 * @method static \Database\Factories\LeaveRequestFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'from_date',
        'to_date',
        'type',
        'reason',
        'evidence_file',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * The student who submitted this leave request
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The admin/teacher who reviewed this request
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}