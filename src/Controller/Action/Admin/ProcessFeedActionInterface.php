<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface ProcessFeedActionInterface
{
    public function __invoke(Request $request, int $id): RedirectResponse;
}
