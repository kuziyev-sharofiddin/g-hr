<?php

namespace App\Enums;

enum FeatureBookStatus: string
{
    case RATING = 'rating';
    case LIKED = 'liked';
    case FINISH_READ = 'finish_read';
    case FUTURE_READ = 'future_read';
    case BOOKMARK = 'bookmark';
}
