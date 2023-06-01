<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ForumController extends Controller {

    /**
     * Get a view browsing the forum threads.
     */
    public function getView(): Response {
        return Inertia::render('Forum/Browse');
    }
}
