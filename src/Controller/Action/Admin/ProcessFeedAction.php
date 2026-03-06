<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Setono\SyliusFeedPlugin\Message\Command\ProcessFeed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProcessFeedAction extends AbstractController implements ProcessFeedActionInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[\Override]
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new ProcessFeed($id));

        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add(
                'success',
                $this->translator->trans('setono_sylius_feed.feed_generation_triggered'),
            );
        }

        return $this->redirectToRoute(
            'setono_sylius_feed_admin_feed_show',
            ['id' => $id],
        );
    }
}
