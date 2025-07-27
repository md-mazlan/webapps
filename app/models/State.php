<?php

/**
 * File: /app/models/State.php
 * Represents a single state record from the database.
 */

class State
{
    public ?int $id;
    public ?string $name;

    public function __construct(?int $id = null, ?string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
