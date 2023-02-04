<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Registry;

use ParadiseSecurity\Component\ServiceRegistry\Exception\ExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;

interface PrioritizedServiceRegistryInterface
{
    public function all(): iterable;

    /**
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($service, int $priority = 0): void;

    /**
     * @param object $service
     *
     * @throws NonExistingServiceException
     */
    public function unregister($service): void;

    /**
     * @param object $service
     */
    public function has($service): bool;
}
