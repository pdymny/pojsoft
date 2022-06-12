<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Lib\AmountToWords;


class WordsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('words', [$this, 'doWords']),
        ];
    }

    public function doWords($value)
    {
        $converter = new AmountToWords();

        return $converter->convert($value);
    }
}
