<?php

namespace App\Application\Interfaces;

/**
 * Interface for CRUD operations with activation and deactivation.
 */
interface ActivatableInterface
{
    /**
     * Reactivate a previously deactivated resource.
     *
     * @return bool Whether the reactivation was successful.
     */
    public function reactivate(): callable;

    /**
     * Deactivate a resource.
     *
     * @return bool Whether the deactivation was successful.
     */
    public function deactivate(): callable;
}
