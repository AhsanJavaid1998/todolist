<?php

namespace App\services;

use App\Contracts\TodoContract;
use App\Models\Todo;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;

class TodoService implements TodoContract
{
    use ApiResponseTrait;
    //list
    public function index($id)
    {
        try {
            $data = Todo::whereUserId($id)->paginate(10);
            $status = true;
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($data ?? null, $status, $message ?? null);
    }

    //search
    public function search($request, $id)
    {
        $validator = Validator::make($request, [
            'title' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        try {
            $data = Todo::whereUserId($id)->where('title', 'LIKE', '%'.$request->title.'%')->paginate(10);
            $status = true;
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($data ?? null, $status, $message ?? null);
    }
    //show
    public function show($todo, $id)
    {
        try {
            if($todo->user_id == $id)
            {
                $todo = [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'description' => $todo->description,
                ];
                $status = true;
                $message = 'Record found successfully';
            }else{
                $todo = [];
                $status = false;
                $message = 'Record not found';
            }
            $data = $todo;
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($data ?? null, $status, $message ?? null);    }
    //create
    public function store($request)
    {
        $validator = Validator::make($request, [
            'title' => 'required',
            'description' => 'required',
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        try {
            $data = Todo::create($request);
            $status = true;
            $message = 'Record create successfully';
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($data ?? null, $status, $message ?? null);
    }

    //update
    public function update($request, $todo, $id)
    {
        $validator = Validator::make($request, [
            'title' => 'required',
            'description' => 'required',
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ApiResponseTrait::response( null, false, $validator->messages());
        }
        try {
            if($todo->user_id == $id)
            {
                $todo->update($request);
                $status = true;
                $message = 'Record update successfully';
            }else{
                $todo = [];
                $status = false;
                $message = 'Record not found';
            }
            $data = $todo;
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response($data ?? null, $status, $message ?? null);
    }

    //destroy
    public function destroy($todo, $id)
    {
        try {
            if($todo->user_id == $id)
            {
                $todo->delete();
                $status = true;
                $message = 'Record delete successfully';
            }else{
                $status = false;
                $message = 'Record not found';
            }
        }catch (\Throwable $th) {
            $status = false;
            $message = $th->getMessage();
        }
        return ApiResponseTrait::response(null, $status, $message ?? null);
    }

}
