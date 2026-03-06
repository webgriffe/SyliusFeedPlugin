<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Setono\SyliusFeedPlugin\Repository\ViolationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SeverityCountAction extends AbstractController implements SeverityCountActionInterface
{
    public function __construct(
        private readonly ViolationRepositoryInterface $violationRepository,
    ) {
    }

    #[\Override]
    public function __invoke(Request $request, ?int $feed = null): Response
    {
        $severityCounts = $this->violationRepository->findCountsGroupedBySeverity($feed);

        return $this->render('@SetonoSyliusFeedPlugin/Admin/Violation/severity_count.html.twig', [
            'severityCounts' => $severityCounts,
        ]);
    }
}
