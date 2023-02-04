<?php

declare(strict_types=1);

namespace ParadiseSecurity\Component\ServiceRegistry\Exception;

use function sprintf;

/**
 * This exception should be thrown by the service registry when a given type already exists.
 */
class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type)
    {
        parent::__construct(sprintf('%s of type "%s" already exists.', $context, $type));
    }
}
