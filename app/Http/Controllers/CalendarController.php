<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller {

    public function show(): Response {
        return Inertia::render('Calendar/Calendar');
    }

    public function submitNewEvent(): \Illuminate\Http\Response {
        // todo
        return response('', 500);
    }

    public function updateEvent(): \Illuminate\Http\Response {
        // todo
        return response('', 500);
    }

}
