<?php


namespace App\Excel;


interface Exporter
{
    public function download($export, string $name);
}