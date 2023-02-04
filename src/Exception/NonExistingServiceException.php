<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Exception;

use function implode;
use function sprintf;
use function ucfirst;

/**
 * This exception should be thrown by the service registry when a given service type does not exist.
 */
class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type, array $existingServices)
    {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices)
        ));
    }
}
