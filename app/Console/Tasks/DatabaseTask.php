<?php

namespace App\Console\Tasks;

use App\Models\Flight;

class DatabaseTask {

    public function __invoke(): void {
        $this->cancelAbandonedFlights();
    }

    private function cancelAbandonedFlights(): void {
        $flights = Flight::getStaleModels();

        foreach ($flights as $flight) {
            $flight->cancelled = true;
            $flight->save();
        }
    }

}
