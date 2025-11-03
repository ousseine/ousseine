<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDto
{
    #[Assert\NotBlank(message: 'Le champ "Nom" ne peut pas être vide')]
    public ?string $name = null;

    #[Assert\Email(message: 'La valeur {{ value }} n\'est pas une adresse email valide')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Le champ "Sujet" ne peut pas être vide')]
    public ?string $subject = null;

    #[Assert\NotBlank(message: 'Le champ "Message" ne peut pas être vide')]
    #[Assert\Length(min: 5, minMessage: 'Le champ "Message" doit contenir au moins {{ limit }} caractères')]
    public ?string $message = null;
}