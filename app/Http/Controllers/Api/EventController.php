<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\CanLoadRelationships;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller implements HasMiddleware
{
    use CanLoadRelationships;
 
    private array $relations = ['user', 'attendees','attendees.user'];
 
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']), 
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = $this->loadRelationships(Event::query(), $request);

        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),
            'user_id' => 1
        ]);

        //$this is related to new event
        return new EventResource($this->loadRelationships($event, $request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Request $request)
    {
        $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event, $request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        Gate::authorize('update-event', $event);

        $event->update($request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time'
        ]));

        return new EventResource($this->loadRelationships($event, $request));
 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response(status: 204);
    }
}
