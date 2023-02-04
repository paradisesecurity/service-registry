<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Registry;

use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;

use function array_column;
use function array_filter;
use function array_keys;
use function array_map;
use function array_multisort;
use function get_class;
use function sprintf;

final class PrioritizedServiceRegistry extends AbstractRegistry implements PrioritizedServiceRegistryInterface
{
    /** @var bool */
    private bool $sorted = true;

    public function all(): iterable
    {
        if ($this->sorted === false) {
            /** @psalm-suppress InvalidPassByReference Doing PHP magic, it works this way */
            array_multisort(
                array_column($this->registry, 'priority'),
                \SORT_DESC,
                array_keys($this->registry),
                \SORT_ASC,
                $this->registry
            );

            $this->sorted = true;
        }

        foreach ($this->registry as $record) {
            yield $record['service'];
        }
    }

    public function register($service, int $priority = 0): void
    {
        $this->assertServiceHaveType($service);

        $this->registry[] = ['service' => $service, 'priority' => $priority];
        $this->sorted = false;
    }

    public function unregister($service): void
    {
        if (!$this->has($service)) {
            throw new NonExistingServiceException(
                $this->context,
                get_class($service),
                array_map('get_class', array_column($this->registry, 'service'))
            );
        }

        $this->registry = array_filter(
            $this->registry,
            static function (array $record) use ($service): bool {
                return $record['service'] !== $service;
            }
        );
    }

    public function has($service): bool
    {
        $this->assertServiceHaveType($service);

        foreach ($this->registry as $record) {
            if ($record['service'] === $service) {
                return true;
            }
        }

        return false;
    }

    private function assertServiceHaveType(object $service): void
    {
        if (!$service instanceof $this->interface) {
            throw new \InvalidArgumentException(sprintf(
                '%s needs to implement "%s", "%s" given.',
                $this->context,
                $this->interface,
                get_class($service)
            ));
        }
    }
}
