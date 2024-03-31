<?php

namespace App\Http\Controllers;

use App\Models\Product; //import model product
use Illuminate\View\View; //import return type view
use Illuminate\Http\RedirectResponse; //import return type RedirectResponse
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() : View{
        $products = Product::latest()->paginate(10); //get all products

        return view('products.index', compact('products')); //render view with product
    }

    /**
     * create
     * 
     * @return View
     */

    public function create(): View{
        return view('products.create');
    }

    /**
     * store
     * 
     * @param mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse{
        //validate form
        $request->validate([
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'price'         => $request->price,
            'stock'         => $request->stock
        ]);

        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
