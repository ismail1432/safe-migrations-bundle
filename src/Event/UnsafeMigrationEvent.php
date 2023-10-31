<?php

namespace Eniams\SafeMigrationsBundle\Event;

/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
class UnsafeMigrationEvent
{
    public function __construct(
        protected UnsafeMigration $unsafeMigration,
    ) {
    }

    public function getUnsafeMigration(): UnsafeMigration
    {
        return $this->unsafeMigration;
    }
}
