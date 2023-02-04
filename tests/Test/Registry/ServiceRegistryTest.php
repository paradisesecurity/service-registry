<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Test\Registry;

use PHPUnit\Framework\TestCase;
use ParadiseSecurity\Component\ServiceRegistry\Exception\ExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Exception\NonExistingServiceException;
use ParadiseSecurity\Component\ServiceRegistry\Registry\ServiceRegistry;
use ParadiseSecurity\Component\ServiceRegistry\Registry\ServiceRegistryInterface;
use ParadiseSecurity\Component\ServiceRegistry\Registry\AbstractRegistry;

use function sprintf;

final class ServiceRegistryTest extends TestCase
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

        $registry->register('date_time', new \DateTime());
        $registry->register('date_time_immutable', new \DateTimeImmutable());

        $this->assertTrue($registry->has('date_time'));
        $this->assertFalse($registry->has('date'));
        $this->assertInstanceOf(\DateTimeInterface::class, $registry->get('date_time'));

        $services = $registry->all();

        $this->assertIsArray($services);
        $this->assertArrayHasKey('date_time', $services);

        $registry->unregister('date_time');

        $this->assertFalse($registry->has('date_time'));
    }

    public function testServiceRegistryCanThrowExceptions(): void
    {
        $registry = $this->createNewServiceRegistry(\DateTimeInterface::class);

        $registry->register('date_time', new \DateTime());

        try {
            $registry->register('date_time', new \DateTime());
            $this->failException(ExistingServiceException::class);
        } catch (ExistingServiceException $exception) {
            $this->assertSame('service of type "date_time" already exists.', $exception->getMessage());
        }

        try {
            $registry->register('date_interval', new \DateInterval('P1Y'));
            $this->failException(\InvalidArgumentException::class);
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(sprintf('Service needs to be of type "%s", "%s" given.', \DateTimeInterface::class, \DateInterval::class), $exception->getMessage());
        }

        try {
            $registry->unregister('date_interval');
            $registry->get('date_interval');
            $this->failException(NonExistingServiceException::class);
        } catch (NonExistingServiceException $exception) {
            $this->assertSame('Service service "date_interval" does not exist, available service services: "date_time"', $exception->getMessage());
        }
    }

    private function failException(string $exception): void
    {
        $this->fail(sprintf('An %s should have been thrown.', $exception));
    }

    private function createNewServiceRegistry(string $interface, string $context = 'service'): ServiceRegistryInterface
    {
        return new ServiceRegistry($interface, $context);
    }
}
