<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Test\Registry;

use PHPUnit\Framework\TestCase;
use ParadiseSecurity\Component\ServiceRegistry\Exception\ExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Registry\PrioritizedServiceRegistry;
use ParadiseSecurity\Component\ServiceRegistry\Registry\PrioritizedServiceRegistryInterface;
use ParadiseSecurity\Component\ServiceRegistry\Registry\AbstractRegistry;

use function iterator_count;

final class PrioritizedServiceRegistryTest extends TestCase
{
    public function testServiceRegistryCanBeCreated()
    {
        $registry = $this->createNewServiceRegistry(\DateTimeInterface::class);

        $reflector = new \ReflectionClass(AbstractRegistry::class);
        $property = $reflector->getProperty('interface');
        $property->setAccessible(true);

        $this->assertSame(\DateTimeInterface::class, $property->getValue($registry));
    }

    public function testServiceCanBeRegistered()
    {
        $registry = $this->createNewServiceRegistry(\DateTimeInterface::class);

        $dateTime = new \DateTime();
        $dateTimeImmutable = new \DateTimeImmutable();

        $registry->register($dateTime);
        $registry->register($dateTimeImmutable, 3);

        $this->assertTrue($registry->has($dateTime));
        $this->assertFalse($registry->has(new \DateTime()));

        $services = $registry->all();
        $count = iterator_count($services);

        $this->assertIsIterable($services);
        $this->assertSame($count, 2);

        $x = 0;
        foreach ($registry->all() as $service) {
            if ($x === 0) {
                $this->assertSame($service, $dateTimeImmutable);
            }
            if ($x === 1) {
                $this->assertSame($service, $dateTime);
            }
            $x++;
        }

        $registry->unregister($dateTime);

        $this->assertFalse($registry->has($dateTime));
    }

    public function testServiceRegistryCanThrowExceptions(): void
    {
        $registry = $this->createNewServiceRegistry(\DateTimeInterface::class);

        $dateTime = new \DateTime();
        $registry->register($dateTime);

        try {
            $registry->register(new \DateInterval('P1Y'));
            $this->failException(\InvalidArgumentException::class);
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(sprintf('service needs to implement "%s", "%s" given.', \DateTimeInterface::class, \DateInterval::class), $exception->getMessage());
        }

        try {
            $registry->unregister(new \DateTimeImmutable());
            $this->failException(NonExistingServiceException::class);
        } catch (NonExistingServiceException $exception) {
            $this->assertSame(sprintf('Service service "%s" does not exist, available service services: "%s"', \DateTimeImmutable::class, \DateTime::class), $exception->getMessage());
        }
    }

    private function failException(string $exception): void
    {
        $this->fail(sprintf('An %s should have been thrown.', $exception));
    }

    private function createNewServiceRegistry(string $interface, string $context = 'service'): PrioritizedServiceRegistryInterface
    {
        return new PrioritizedServiceRegistry($interface, $context);
    }
}
