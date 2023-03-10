<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Registry;

use ParadiseSecurity\Component\ServiceRegistry\Exception\ExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;

interface ServiceRegistryInterface
{
    public function all(): array;

    /**
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register(string $identifier, $service): void;

    /**
     * @throws NonExistingServiceException
     */
    public function unregister(string $identifier): void;

    public function has(string $identifier): bool;

    /**
     * @return object
     *
     * @throws NonExistingServiceException
     */
    public function get(string $identifier);
}
