<?php

namespace App\Enums;

enum BookStatus: string
{
    case UNRECOMMENDED = 'unrecommended'; // tavsiya qilinmagan kitoblar statusi.
    case ALL = 'all'; // faqat hammasini olishda ishlatiladi.Databasega saqlanmaydi.
    case RECOMMENDED = 'recommended'; // tavsiya qilingan kitoblar statusi.
}
