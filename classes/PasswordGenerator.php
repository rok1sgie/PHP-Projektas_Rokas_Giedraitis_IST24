<?php

declare(strict_types=1);

class PasswordGenerator
{
    private int $length;
    private int $lowercase;
    private int $uppercase;
    private int $numbers;
    private int $specials;

    private string $lowerChars = 'abcdefghijklmnopqrstuvwxyz';
    private string $upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $numberChars = '0123456789';
    private string $specialChars = '!@#$%^&*()_+-=.,?';

    public function __construct(int $length, int $lowercase, int $uppercase, int $numbers, int $specials)
    {
        $this->length = $length;
        $this->lowercase = $lowercase;
        $this->uppercase = $uppercase;
        $this->numbers = $numbers;
        $this->specials = $specials;
    }

    private function getRandomChars(string $pool, int $count): array
    {
        $chars = [];
        $maxIndex = strlen($pool) - 1;

        for ($i = 0; $i < $count; $i++) {
            $chars[] = $pool[random_int(0, $maxIndex)];
        }

        return $chars;
    }

    public function generate(): string
    {
        if ($this->length <= 0) {
            throw new Exception('Slaptažodžio ilgis turi būti teigiamas.');
        }

        $sum = $this->lowercase + $this->uppercase + $this->numbers + $this->specials;
        if ($sum !== $this->length) {
            throw new Exception('Įvestų simbolių suma turi būti lygi nurodytam ilgiui.');
        }

        $passwordArray = array_merge(
            $this->getRandomChars($this->lowerChars, $this->lowercase),
            $this->getRandomChars($this->upperChars, $this->uppercase),
            $this->getRandomChars($this->numberChars, $this->numbers),
            $this->getRandomChars($this->specialChars, $this->specials)
        );

        shuffle($passwordArray);

        return implode('', $passwordArray);
    }
}