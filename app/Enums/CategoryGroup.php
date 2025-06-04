<?php

namespace App\Enums;

use App\Models\CategoryType;

enum CategoryGroup: string
{
    case EVENT = 'event';
    case NEWS = 'news';
    case ARTICLE = 'article';

    public function getTypes()
    {
        return CategoryType::getTypesForGroup($this);
    }


}
