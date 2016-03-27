<?php

namespace Domynation\Entities;

interface Identifiable
{

    /**
     * Sets the identify of an entity.
     *
     * @param string $identity
     */
    public function setIdentity($identity);
}