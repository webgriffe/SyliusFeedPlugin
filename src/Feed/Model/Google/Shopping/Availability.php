<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping;

enum Availability: string
{
    case backOrder = 'backorder';
    case inStock = 'in_stock';
    case outOfStock = 'out_of_stock';
    case preOrder = 'preorder';
}
