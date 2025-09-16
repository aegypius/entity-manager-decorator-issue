<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\VarExporter\LazyObjectInterface;
use Symfony\Component\VarExporter\LazyProxyTrait;

#[Lazy, AsDecorator('doctrine.orm.default_entity_manager')]
class DecoratedEntityManager extends EntityManagerDecorator implements LazyObjectInterface
{
    use LazyProxyTrait;

    public function __construct(
        #[AutowireDecorated]
        EntityManagerInterface $wrapped
    ) {
        parent::__construct($wrapped);
    }

    #[\Override]
    public function resetLazyObject(): bool
    {
        if ($this->wrapped instanceof LazyObjectInterface) {
            return $this->wrapped->resetLazyObject();
        }

        return false;
    }
}
