<?php

namespace Domynation\Contracts;

interface SoftDeletable
{
    /**
     * Soft deletes an entity.
     */
    public function delete();
}