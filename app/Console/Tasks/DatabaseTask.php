<?php

namespace App\Console\Tasks;

use App\Models\Flight;

class DatabaseTask {

    public function __invoke(): void {
        $this->insertFlights();
        $this->deleteFlights();
    }

    private function insertFlights(): void {
        // todo method stub
    }

    private function deleteFlights(): void {
        // todo method stub
    }

}
