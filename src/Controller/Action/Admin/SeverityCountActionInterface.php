<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface SeverityCountActionInterface
{
    public function __invoke(Request $request, ?int $feed = null): Response;
}
