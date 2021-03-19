<?php


namespace App\Excel;


use App\Model\Model;

interface Importer
{
    /**
     * @param object $import
     * @param string $name
     * @return mixed
     */
    public function import(object $import, string $name);
}