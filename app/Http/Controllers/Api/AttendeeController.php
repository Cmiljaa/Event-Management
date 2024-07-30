<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\CanLoadRelationships;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event, Request $request)
    {
        $attendees = $event->attendees()->latest()->paginate();

        return AttendeeResource::collection($this->loadRelationships($event, $request));
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
    public function destroy(string $event, Attendee $attendee)
    {
        $attendee->delete();

        return response(status: 204);
    }
}
