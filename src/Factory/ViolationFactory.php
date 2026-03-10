<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Factory;

use Setono\SyliusFeedPlugin\Model\ViolationInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

final readonly class ViolationFactory implements ViolationFactoryInterface
{
    /**
     * @param FactoryInterface<ViolationInterface> $decoratedFactory
     */
    public function __construct(
        private FactoryInterface $decoratedFactory,
    ) {
    }

    public function createNew(): ViolationInterface
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * @param mixed|null $data
     */
    public function createFromConstraintViolation(
        ConstraintViolationInterface $constraintViolation,
        ChannelInterface $channel,
        LocaleInterface $locale,
        $data = null,
    ): ViolationInterface {
        $violation = $this->createNew();

        $violation->setChannel($channel);
        $violation->setLocale($locale);
        $violation->setMessage(
            /** @phpstan-ignore cast.string */
            $constraintViolation->getPropertyPath() . ': ' . sprintf((string) $constraintViolation->getMessage(), (string) $constraintViolation->getInvalidValue()),
        );
        $violation->setData($data);

        if ($constraintViolation instanceof ConstraintViolation) {
            $constraint = $constraintViolation->getConstraint();
            if (null !== $constraint && is_array($constraint->payload) && isset($constraint->payload['severity']) && is_string($constraint->payload['severity'])) {
                $violation->setSeverity($constraint->payload['severity']);
            }
        }

        return $violation;
    }
}
