<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Registry;

abstract class AbstractRegistry
{
    /**
     * @var array
     */
    protected array $registry = [];

    public function __construct(
        protected string $interface,
        protected string $context = 'service',
    ) {
    }
}
