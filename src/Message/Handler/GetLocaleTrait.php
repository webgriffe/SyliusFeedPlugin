<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Message\Handler;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

trait GetLocaleTrait
{
    /** @var RepositoryInterface<LocaleInterface> */
    private RepositoryInterface $localeRepository;

    private function getLocale(int $id): LocaleInterface
    {
        $obj = $this->localeRepository->find($id);

        if (!$obj instanceof LocaleInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('Locale with id %s does not exist', $id));
        }

        return $obj;
    }
}
