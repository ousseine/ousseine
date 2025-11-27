<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

class FormatDateTimeExtension
{
    #[AsTwigFilter('format_time_diff')]
    public function formatTime($date): string
    {
        $diff = (new \DateTimeImmutable())->diff($date);

        $units = [
            ['label' => 'année', 'value' => $diff->y],
            ['label' => 'moi', 'value' => $diff->m],
            ['label' => 'jour', 'value' => $diff->d],
            ['label' => 'heure', 'value' => $diff->h],
            ['label' => 'minute', 'value' => $diff->i],
        ];

        foreach ($units as $unit) {
            if ($unit['value'] > 0) {
                return sprintf('Il y a %d %s%s', $unit['value'], $unit['label'], $unit['value'] > 1 ? 's' : '');
            }
        }

        return sprintf('Il y a %d minute%s', $diff->i, $diff->i > 1 ? 's' : '');
    }
}