<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$categories = Category::all();
		return response()->json([
            'message' => 'Categories fetched successfully',
            'data' => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'slug'=>'required',
            'name'=>'required',
            'is_active'=>'required|numeric',
         ]);
		 
         if ($validator->fails()) {
            return response()->json([
                'message' => 'Validations failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = Category::create([
            'slug' => $request->slug,
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);
		
        return response()->json([
            'message' => 'Category created Successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
		return response()->json([
            'message' => 'Category fetched Successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->slug = $request->slug;
        $category->name = $request->name;
        $category->is_active = $request->is_active;
        $category->save();

        return response()->json([
            'message' => 'Category updated Successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
		$result = $category->delete();
		if ($result) {
			return response()->json(['message' => 'Deleted Successfully'], 200);
		}
    }
}
