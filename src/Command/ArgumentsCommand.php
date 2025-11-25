<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;

final class ArgumentsCommand
{
    public function askArguments(InputInterface $input, string $name, $io, ?bool $hidden = false): void
    {
        $value = strval($input->getArgument($name));

        if ('' !== $value) {
            $io->text(sprintf('> <info>%s</info>: %s', $name, $value));
        } else {
            $value = match ($hidden) {
                false => $io->ask($name),
                default => $io->askHidden($name),
            };
            $input->setArgument($name, $value);
        }
    }
}