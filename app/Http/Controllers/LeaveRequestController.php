<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isStudent()) {
            $leaveRequests = LeaveRequest::with('reviewer')
                ->where('student_id', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            // Admin and teachers can see all requests
            $query = LeaveRequest::with(['student', 'reviewer']);
            
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('student_id') && $request->student_id) {
                $query->where('student_id', $request->student_id);
            }
            
            $leaveRequests = $query->latest()->paginate(10);
        }
        
        $students = $user->isStudent() ? null : User::students()->get();
        
        return Inertia::render('leave-requests/index', [
            'leave_requests' => $leaveRequests,
            'students' => $students,
            'filters' => $request->only(['status', 'student_id']),
            'user_role' => $user->role,
        ]);
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $user = auth()->user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can create leave requests.');
        }
        
        return Inertia::render('leave-requests/create');
    }

    /**
     * Store a newly created leave request.
     */
    public function store(StoreLeaveRequestRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        
        $leaveRequest = new LeaveRequest($validated);
        $leaveRequest->student_id = $user->id;
        
        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $path = $file->store('leave-requests', 'public');
            $leaveRequest->evidence_file = $path;
        }
        
        $leaveRequest->save();
        
        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        
        // Students can only view their own requests
        if ($user->isStudent() && $leaveRequest->student_id !== $user->id) {
            abort(403);
        }
        
        $leaveRequest->load(['student', 'reviewer']);
        
        return Inertia::render('leave-requests/show', [
            'leave_request' => $leaveRequest,
            'user_role' => $user->role,
        ]);
    }

    /**
     * Update the status of a leave request (approve/reject).
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isTeacher()) {
            abort(403, 'Only admins and teachers can review leave requests.');
        }
        
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $leaveRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request has been ' . $request->status . '.');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        
        // Students can only delete their own pending requests
        if ($user->isStudent()) {
            if ($leaveRequest->student_id !== $user->id || $leaveRequest->status !== 'pending') {
                abort(403, 'You can only delete your own pending requests.');
            }
        }
        
        // Delete evidence file if exists
        if ($leaveRequest->evidence_file) {
            Storage::disk('public')->delete($leaveRequest->evidence_file);
        }
        
        $leaveRequest->delete();
        
        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }
}