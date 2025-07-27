<?php

class EthnicGroup
{
    public ?int $id;
    public ?string $name;
    public ?string $category;

    public function __construct(?int $id = null, ?string $name = null, ?string $category = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
    }
}
