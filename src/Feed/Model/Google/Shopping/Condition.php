<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping;

enum Condition: string
{
    case new = 'new';
    case refurbished = 'refurbished';
    case used = 'used';
}
