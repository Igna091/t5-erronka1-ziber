<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(10);
        $enrollments->appends($request->query());

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Cancel an enrollment.
     */
    public function cancel(Enrollment $enrollment)
    {
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Función deshabilitada (Modo solo diseño).');
    }

    /**
     * Reactivate an enrollment.
     */
    public function reactivate(Enrollment $enrollment)
    {
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Función deshabilitada (Modo solo diseño).');
    }
}
