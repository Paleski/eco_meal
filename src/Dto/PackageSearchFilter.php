<?php

namespace App\Dto;

use App\Entity\Business;
use App\Entity\Category;
use App\Entity\Consumer;

class PackageSearchFilter
{
    public ?string $name = null;
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public ?Category $category = null;
    //todo: add more filters (BusinessType, Business, City, etc)
}
