<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityResource;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Notifications\RoleChangedNotification;
use App\Models\Order;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::paginate();
        return Inertia::render('Admin/Employees/Index', ['employees' => UserResource::collection($employees)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Employees/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),

        ]);
    }


    public function show(Request $request, User $employee)
    {

        $activities = Activity::where('causer_id', $employee->id)
            ->where('causer_type', User::class)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
  $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $stats = Activity::where('causer_id', $employee->id)
            ->where('causer_type', User::class)
            ->whereIn('description', [
                'order.created', 'order.confirmed', 'order.shipped', 'order.delivered',
                'order.canceled', 'order.returned', 'order.nrp'
            ])
            ->when($startDate, function($query) use ($startDate) {
                 return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                 return $query->whereDate('created_at', '<=', $endDate);
            })
            ->selectRaw('description, count(*) as count')
            ->groupBy('description')
            ->pluck('count', 'description');
        $confirmedOrderIds = Activity::where('causer_id', $employee->id)
            ->where('causer_type', User::class)
            ->where('description', 'order.confirmed')
            ->where('subject_type', Order::class)
            ->when($startDate, function($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->pluck('subject_id');

        $deliveryStats = Order::whereIn('id', $confirmedOrderIds)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        return Inertia::render('Admin/Employees/Show', [
            'employee' => UserResource::make($employee),
            'activities' => ActivityResource::collection($activities),
             'stats' => $stats,
             'deliveryStats' => $deliveryStats,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        return Inertia::render('Admin/Employees/Edit', ['employee' => $employee]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $validated = $request->validated();

        $oldRole =  $employee->role;

        $employee->update($validated);

        if ($employee->role != $oldRole) {
            $employee->notify(new RoleChangedNotification);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        foreach ($employee->notifications as $notification) {
            $notification->delete();
        }
        foreach ($employee->notes as $note) {
            $note->delete();
        }
        $employee->delete();

        return redirect()->route('employees.index');
    }

    public function clean()
    {
        // \Illuminate\Support\Facades\Artisan::call('activitylog:clean');
        // return redirect()->back()->with('success', 'Activity log cleaned successfully.');
    }
}
