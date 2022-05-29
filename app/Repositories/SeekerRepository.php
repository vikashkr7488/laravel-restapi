<?php

namespace App\Repositories;

use App\Models\Seeker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class SeekerRepository extends BaseRepository
{

    protected $model;

    public function __construct()
    {
        $this->model = new Seeker;
    }
    
    /**  
     * Get query builder and apply filters
     * 
     * @param payload
     * @return collection
    */
    public function getBuilder($payload)
    { 
        $builder = $this->model->with(['user', 'category']);
        
        if ($name = Arr::get($payload, 'name')) {
            $builder = $builder->where('name', 'LIKE', "%{$name}%");
        }
        
        if ($category = Arr::get($payload, 'category')) {
            $builder = $builder->whereHas('category', function($query) use ($category){
                $query->where('name', $category);
            });
        }

        if ($location = Arr::get($payload, 'location')) {
            $builder = $builder->where('location', 'LIKE', "%{$location}%");
        }

        if (Arr::get($payload, 'sortBy')) {
            $sortBy = $payload['sortBy'];
        } else {
            $sortBy = 'id';
        }

        if (Arr::get($payload, 'sortOrder')) {
            $sortOrder = $payload['sortOrder'];
        } else {
            $sortOrder = 'DESC';
        }
        
        if (Arr::get($payload, 'perPage')) {
            $perPage = $payload['perPage'];
        } else {
            $perPage = 5;
        }

        if ($paginate = Arr::get($payload, 'paginate')) {
            $builder = $builder->orderBy($sortBy, $sortOrder)->paginate($perPage);
        } else {
            $builder = $builder->orderBy($sortBy, $sortOrder)->get();
        }

        return response()->json([
            'message' => 'Job Profile fetched Successfully',
            'data' => $builder,
        ], 200);
    }

    public function addJobSeeker($payload, $userId) 
    {
        $validator = Validator::make($payload, [
            'category_id' => 'required',
            'name' => 'required|max:255',
            'image' => 'required|image|mimes:jpg,jeg,png',
            'phone' => 'required',
            'location' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validations failed',
                'errors' => $validator->errors(),
            ], 422);
        }

		// $imageName = time().".".$payload['image']->extention();
        // $payload->image->move(public_path('uploads/user_images'), $imageName);

        $seeker = $this->model->create([
            'user_id' => $userId,
            'category_id' => $payload['category_id'],
            'name' => $payload['name'],
            'image' => $payload['image']->store('images'),
            'phone' => $payload['phone'],
            'location' => $payload['location'],
            'description' => $payload['description'],
        ]);

        $seeker->load('user:id,name,email', 'category:id,name');

        return response()->json([
            'message' => 'Job Profile created Successfully',
            'data' => $seeker,
        ], 201);
    }

    public function seekerShow($id)
    {
        $jobSeeker = $this->model->with(['user', 'category'])->where('id', $id)->first();
        return $jobSeeker;
    }

    public function updateJobSeeker($payload, $userId, $id)
    {
        $jobSeeker = $this->seekerShow($id);
        if ($jobSeeker) {
            if ($jobSeeker->user_id == $userId) {
                $validator = Validator::make($payload, [
                    'category_id' => 'required',
                    'name' => 'required|max:255',
                    'image' => 'nullable|image|mimes:jpg,jeg,png',
                    'phone' => 'required',
                    'location' => 'required',
                    'description' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validations failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $seeker = $jobSeeker->update([
                    'category_id' => $payload['category_id'],
                    'name' => $payload['name'],
                    'image' => $payload['image']->store('images'),
                    'phone' => $payload['phone'],
                    'location' => $payload['location'],
                    'description' => $payload['description'],
                ]);

                return response()->json([
                    'message' => 'Job Profile updated Successfully',
                    'data' => $seeker,
                ], 200);

            } else {
                return response()->json([
                    'message' => 'Access Denied'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Job Profile not found'
            ], 400);
        }
    }

    public function findJobSeeker($id)
    {
        $jobSeeker = $this->model->findOrFail($id);
        return $jobSeeker;
    }
}