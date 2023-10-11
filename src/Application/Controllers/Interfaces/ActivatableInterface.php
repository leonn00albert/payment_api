<?php

namespace App\Application\Controllers\Interfaces;

/**
 * Interface for CRUD operations with activation and deactivation.
 */
interface ActivatableInterface
{
    /**
     * Reactivate a previously deactivated resource.
     *
     * @return callable Whether the reactivation was successful.
     */
    public function reactivate(): callable;

    /**
     * Deactivate a resource.
     *
     * @return callable Whether the deactivation was successful.
     */
    public function deactivate(): callable;
}
