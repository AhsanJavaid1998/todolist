<?php

namespace App\Contracts;

interface TodoContract
{
    public function index($id);
    public function search($title, $id);
    public function store($request);
    public function show($todo, $id);
    public function update($request, $todo, $id);
    public function destroy($todo, $id);
}
