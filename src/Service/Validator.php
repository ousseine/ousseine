<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Validator
{
    public function __construct(
        private ValidatorInterface $validator
    ){
    }

    public function validateEmail(string $email): string
    {
        $this->notBlank($email);

        $constraint = new Assert\Email();
        $this->validation($email, $constraint);

        return $email;
    }

    public function validatePassword(string $password): string
    {
        $this->notBlank($password);

        $constraint = new Assert\Length(['min' => 8, 'max' => 4096]);
        $this->validation($password, $constraint);

        return $password;
    }

    private function validation($value, $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);

        if (count($violations) > 0)
            throw new \InvalidArgumentException((string) $violations);
    }

    private function notBlank(string $value): void
    {
        if (empty($value)) throw new \InvalidArgumentException("Le champ $value ne peux pas être vide.");
    }
}