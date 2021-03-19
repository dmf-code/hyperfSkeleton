<?php


namespace App\Excel\concerns;


use Hyperf\Utils\Collection;

interface FromCollection
{
    /**
     * @return array
     */
    public function collection(): array;
}