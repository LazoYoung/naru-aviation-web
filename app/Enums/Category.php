<?php

namespace App\Enums;

enum Category: string {
    case GENERAL = "General";
    case QUESTION = "Question";
    case NOTAM = "NOTAM";
    case EVENT = "Event";
}
