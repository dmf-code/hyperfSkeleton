<?php


namespace App\Excel\concerns;


use Hyperf\Utils\Collection;

interface ToCollection
{
    /**
     * @param Collection $collection
     * @param $startRow
     * @return array
     */
    public function collection(Collection $collection, $startRow): array;
}