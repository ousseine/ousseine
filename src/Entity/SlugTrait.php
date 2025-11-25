<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_SLUG', fields: ['slug'])]
trait SlugTrait
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}