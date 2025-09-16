<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $container, FrameworkConfig $framework): void {
    $framework
        ->secret(env('APP_SECRET'))
        ->session()->enabled(false);

    if ('test' === $container->env()) {
        $framework->test(true);
    }
};
