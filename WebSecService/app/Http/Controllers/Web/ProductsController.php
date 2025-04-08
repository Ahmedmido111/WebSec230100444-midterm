<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsController extends Controller {

	use ValidatesRequests;

	public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

	public function list(Request $request) {

		$query = Product::select("products.*");

		$query->when($request->keywords, 
		fn($q)=> $q->where("name", "like", "%$request->keywords%"));

		$query->when($request->min_price, 
		fn($q)=> $q->where("price", ">=", $request->min_price));
		
		$query->when($request->max_price, fn($q)=> 
		$q->where("price", "<=", $request->max_price));
		
		$query->when($request->order_by, 
		fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));

		$products = $query->get();

		return view('products.list', compact('products'));
	}

	public function edit(Request $request, Product $product = null) {

		if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('employee')) {
			abort(403, 'Unauthorized action.');
		}

		if (!$product) {
			$product = new Product();
		}

		return view('products.edit', compact('product'));
	}

	public function save(Request $request, Product $product = null) {

		if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('employee')) {
			abort(403, 'Unauthorized action.');
		}

		if (!$product) {
			$product = new Product();
		}

		$this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
	        'description' => ['required', 'string', 'max:1024'],
	        'price' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB max
	    ]);

		$product->fill($request->except('photo'));

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
            
            // Store new photo
            $path = $request->file('photo')->store('products', 'public');
            $product->photo = $path;
        }

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
            $product->photo = null;
        }

		$product->save();

		return redirect()->route('products_list')->with('success', 'Product saved successfully.');
	}

	public function delete(Request $request, Product $product) {

		if (!auth()->user()->hasRole('admin')) {
			abort(403, 'Unauthorized action.');
		}

        // Delete photo if exists
        if ($product->photo) {
            Storage::disk('public')->delete($product->photo);
        }

		$product->delete();

		return redirect()->route('products_list')->with('success', 'Product deleted successfully.');
	}
} 