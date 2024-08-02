<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\CanLoadRelationships;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user'];

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event, Request $request)
    {
        $attendees = $this->loadRelationships(
            $event->attendees()->latest(), $request
        );

        //This line is problem
        return AttendeeResource::collection($attendees->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => 1
        ]);

        return new AttendeeResource($this->loadRelationships($event, $request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee, Request $request)
    {
        return new AttendeeResource($this->loadRelationships($event, $request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        Gate::authorize('delete-attendee', $event, $attendee);

        $attendee->delete();

        return $event;
    }
}
