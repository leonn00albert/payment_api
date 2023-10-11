<?php

namespace App\Application\Controllers\Interfaces;

/**
 * Interface for CRUD operations.
 */
interface CrudInterface
{
    /**
     * Create a new resource.
     *
     * @return callable A callable function that handles the create operation.
     */
    public function create(): callable;

    /**
     * Read a resource by its identifier.
     *
     * @return callable A callable function that handles the read operation.
     */
    public function read(): callable;

    /**
     * Update an existing resource.
     *
     * @return callable A callable function that handles the update operation.
     */
    public function update(): callable;

    /**
     * Delete a resource by its identifier.
     *
     * @return callable A callable function that handles the delete operation.
     */
    public function delete(): callable;
}
