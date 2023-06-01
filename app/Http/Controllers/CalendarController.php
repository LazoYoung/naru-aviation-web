<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller {

    public function show(): Response {
        return Inertia::render('Calendar/Calendar');
    }

}
