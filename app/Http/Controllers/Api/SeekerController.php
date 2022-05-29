<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seeker;
use Illuminate\Http\Request;
use App\Repositories\SeekerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; 

class SeekerController extends Controller
{
    public function __construct(SeekerRepository $repository)
	{
		$this->repository = $repository;
	}

    public function index(Request $request)
    {
        $data = $request->all();
        $jobSeeker = $this->repository->getBuilder($data);
        return $jobSeeker;
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $userId = Auth::user()->id;
        $jobSeeker = $this->repository->addJobSeeker($data, $userId);
        
        return $jobSeeker;
    }

    public function seekerShow($id){
        $jobSeeker = $this->repository->seekerShow($id);
        if ($jobSeeker) {
            return response()->json([
                'message' => 'Job Profile fetched Successfully',
                'data' => $jobSeeker,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Job Profile not found'
            ], 400);
        }
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $userId = Auth::user()->id;
        $jobSeeker = $this->repository->updateJobSeeker($data, $userId, $id);
        return $jobSeeker;
    }

    public function delete(Request $request, $id){
        //$jobSeeker = $this->repository->findJobSeeker($id);
        $jobSeeker = Seeker::where('id', $id)->first();
        if ($jobSeeker) {
            if ($jobSeeker->user_id==$request->user()->id) {
                $oldPath = public_path().'images'.$jobSeeker->image;
                if (File::exists($oldPath)) {
                    File::delete(($oldPath));
                }

                $deleted = $jobSeeker->delete();
                if ($deleted) {
                    return response()->json([
                        'message' => 'Job Profile deleted Successfully',
                    ], 200);
                }

            } else {
                return response()->json([
                    'message' => 'Access Denied',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Job Profile not found'
            ], 400);
        }
    }
}
