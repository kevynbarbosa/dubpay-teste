<?php

namespace App\DTO;

interface DTO
{
    public static function fromArray(array $data): self;

    public static function toArray(self $dto): array;
}
