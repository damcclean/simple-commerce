<?php

namespace DoubleThreeDigital\SimpleCommerce\Contracts;

interface ProductRepository
{
    public function query();

    public function all();

    public function find($id);

    public function findBySlug(string $slug);

    public function save($entry);

    public function update($id, $entry);

    public function delete($entry);

    public function createRules();

    public function updateRules($entry);
}
