<?php

namespace App\Repositories;

use App\Interfaces\CarRepositoryInterface;
use App\Models\Car;

class CarRepository implements CarRepositoryInterface
{
    public function index()
    {
        return Car::all();
    }

    public function getById($id)
    {
        return Car::findOrFail($id);
    }

    public function store(array $data)
    {
        return Car::create($data);
    }

    public function update(array $data, $id)
    {
        return Car::whereId($id)->update($data);
    }

    public function delete($id)
    {
        Car::destroy($id);
    }
}
