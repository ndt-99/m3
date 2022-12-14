<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{

    public function index()
    {
        $items=Category::all();
        return view('categories.index',compact('items'));
    }

    public function create()
    {
        return view('categories.add');
    }

    public function store(Request $request)
    {
        $item= new Category();
        $item->name=$request->name;
        $item->save();
        return redirect()->route('categories.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $item=Category::find($id);
        return view('categories.edit',compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item=Category::find($id);
        $item->name=$request->name;
        $item->save();
        return redirect()->route('categories.index');
    }

    public function destroy($id)
    {
        $item=Category::findOrFail($id);
        $item->delete();
        return redirect()->route('categories.index');
    }
}
