<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
   // set index page view
	public function index() {
		return view('index');
	}

	// handle fetch all product ajax request
	public function fetchAll() {
		$products = Product::all();
		$output = '';
		if ($products->count() > 0) {
			$output .= '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Price</th>
				<th>upc</th>
				<th>Description</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>';
			foreach ($products as $emp) {
				
				$output .= '<tr>
                <td>' . $emp->id . '</td>
                <td><img src="storage/images/' . $emp->avatar . '" width="50" class="img-thumbnail rounded-circle"></td>
                <td>' . $emp->name . '</td>
                <td>' . $emp->price . '</td>
                <td>' . $emp->upc . '</td>
                <td>' . $emp->discription . '</td>
                <td>
                  <a href="#" id="' . $emp->id . '" class="text-success mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editProductModal"><i class="bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $emp->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
			}
			$output .= '</tbody></table>';
			echo $output;
		} else {
			echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
		}
	}

	// handle insert a new product ajax request
	public function store(Request $request) {
		
		$file = $request->file('avatar');
		$fileName = time() . '.' . $file->getClientOriginalExtension();
		$file->storeAs('public/images', $fileName);

		$empData = ['name' => $request->name, 'price' => $request->price,  'upc' => $request->upc, 'discription' => $request->discription, 'avatar' => $fileName];
		
		Product::create($empData);
		return response()->json([
			'status' => 200,
		]);
	}

	// handle edit an product ajax request
	public function edit(Request $request) {
		$id = $request->id;
		$emp = Product::find($id);
		return response()->json($emp);
	}

	// handle update an product ajax request
	public function update(Request $request) {
		$fileName = '';
		$emp = Product::find($request->id);
		if ($request->hasFile('avatar')) {
			$file = $request->file('avatar');
			$fileName = time() . '.' . $file->getClientOriginalExtension();
			$file->storeAs('public/images', $fileName);
			if ($emp->avatar) {
				Storage::delete('public/images/' . $emp->avatar);
			}
		} else {
			$fileName = $request->emp_avatar;
		}

		$empData = ['name' => $request->name, 'price' => $request->price, 'upc' => $request->upc, 'description' => $request->description,  'avatar' => $fileName];
       
		$emp->update($empData);
		return response()->json([
			'status' => 200,
		]);
	}

	// handle delete an product ajax request
	public function delete(Request $request) {
		$id = $request->id;
		$emp = Product::find($id);
		if (Storage::delete('public/images/' . $emp->avatar)) {
			Product::destroy($id);
		}
	}
}
