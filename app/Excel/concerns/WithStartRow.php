<?php


namespace App\Excel\concerns;


interface WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int;
}