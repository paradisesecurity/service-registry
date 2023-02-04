<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Registry;

use ParadiseSecurity\Component\ServiceRegistry\Exception\ExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;

use function array_keys;
use function get_class;
use function sprintf;
use function ucfirst;

/**
 * Cannot be final, because it is proxied
 */
class ServiceRegistry extends AbstractRegistry implements ServiceRegistryInterface
{
    public function all(): array
    {
        return $this->registry;
    }

    public function register(string $identifier, $service): void
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!$service instanceof $this->interface) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to be of type "%s", "%s" given.', ucfirst($this->context), $this->interface, get_class($service))
            );
        }

        $this->registry[$identifier] = $service;
    }

    public function unregister(string $identifier): void
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->registry));
        }

        unset($this->registry[$identifier]);
    }

    public function has(string $identifier): bool
    {
        return isset($this->registry[$identifier]);
    }

    public function get(string $identifier): object
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->registry));
        }

        return $this->registry[$identifier];
    }
}
