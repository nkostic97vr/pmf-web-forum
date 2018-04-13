<?php

namespace App\Http\Controllers\Sections;

use Session;
use Validator;
use App\Forum;
use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends SectionsController
{
    protected $table = 'categories';
    protected $model = 'App\Category';

    public function index() {
        return parent::index();
    }

    public function edit($id) {
        return parent::edit($id);
    }

    public function update(Request $request, $id) {
        return parent::update($request, $id);
    }

    public function destroy($id) {
        return parent::destroy($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255|unique:categories'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category = new Category;
        $category->title = $request->title;
        $category->description = e($request->description);
        $category->position = Category::max('position') + 1;
        $category->save();

        Session::flush("Category successfully created.");
        return redirect(route('categories.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($category = Category::where('id', $id)->first()) {
            return view('admin.categories.show')->with('category', $category);
        }
    }

}
