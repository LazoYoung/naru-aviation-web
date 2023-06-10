<?php

namespace App\Enums;

use InvalidArgumentException;

enum Category: string {
    case GENERAL = "General";
    case QUESTION = "Question";
    case NOTAM = "NOTAM";
    case EVENT = "Event";

    public static function byId(int $id): Category {
        return match ($id) {
            0 => Category::GENERAL,
            1 => Category::QUESTION,
            2 => Category::NOTAM,
            3 => Category::EVENT,
            default => throw new InvalidArgumentException("Invalid id: " . $id),
        };
    }

    public static function getId(Category $category): int {
        return match ($category) {
            Category::GENERAL => 0,
            Category::QUESTION => 1,
            Category::NOTAM => 2,
            Category::EVENT => 3,
        };
    }
}
