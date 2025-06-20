<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FestivalEvent;

class EventController extends Controller
{
    //
    public function event(Request $request)
    {
        $festivals = FestivalEvent::get();
        return view('events',compact('festivals'));
    }

    public function createEvent(Request $request)
    {
        $request->validate([
            'events' => 'required',
            'event_date' => 'required'
        ]);
        $events = $request->events;
        $event_date = $request->event_date;

        FestivalEvent::create([
            'events' => $events,
            'event_date' => $event_date
        ]);
        
        return response()->json(['success' => true, 'message' => 'Event Created Successfully']);
    }

    public function showEditEvent(Request $request)
    {   
        $request->validate([
            'event_id' => 'required',
        ]);
        $event_id = $request->input('event_id');
        $event = FestivalEvent::where('_id', $event_id)->first();

        return view('edit-event', compact('event'));
    }

    public function updateEvent(Request $request)
    {
        $request->validate([
            'event_id' => 'required',
            'events' => 'required',
            'event_date' => 'required'
        ]);
        $event_id = $request->input('event_id');
        $event = FestivalEvent::where('_id', $event_id)->first();
        $event->events = $request->input('events');
        $event->event_date = $request->input('event_date');
        $event->save();

        return response()->json(['success' => true, 'message' => 'Event updated successfully']);
    }
}
