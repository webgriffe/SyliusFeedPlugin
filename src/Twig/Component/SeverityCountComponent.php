<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Twig\Component;

use Setono\SyliusFeedPlugin\DTO\SeverityCount;
use Setono\SyliusFeedPlugin\Repository\ViolationRepositoryInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SeverityCountComponent
{
    use TemplatePropTrait;

    public ?int $feed = null;

    public function __construct(
        private readonly ViolationRepositoryInterface $violationRepository,
    ) {
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    /**
     * @return SeverityCount[]
     */
    public function getCounts(): array
    {
        return $this->violationRepository->findCountsGroupedBySeverity($this->feed);
    }
}
