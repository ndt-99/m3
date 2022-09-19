<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function index()
    {
        // $items = Product::all();
        $items = Product::paginate(3);
        $suppliers=Supplier::all();
        return view('admin.products.index', compact('items','suppliers'));
    }

    public function create()
    {
        $items = Category::all();
        $suppliers=Supplier::all();
        return view('admin.products.add', compact('items','suppliers'));
    }

    public function store(ProductCreateRequest $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // dd(Auth()->user());
        $items = Category::all();
        $suppliers=Supplier::all();

        $param=[
            'items'=>$items,
            'request'=>$request,
            'suppliers'=>$suppliers,
        ];
        $item = new Product();
        $item->name = $request->name;
        $item->age = $request->age;
        $item->color = $request->color;
        $item->gender = $request->gender;
        $item->price = $request->price;
        $item->quantity = $request->quantity;
        $item->category_id = $request->category_id;
        $item->supplier_id = $request->supplier;
        $item->user_id=Auth()->user()->id;
        $file = $request->inputFile;

        if ($request->hasFile('inputFile')) {
            $fileExtension = $file->getClientOriginalName();
            //Lưu file vào thư mục storage/app/public/image với tên mới
            $request->file('inputFile')->storeAs('public/images', $fileExtension);
            // Gán trường image của đối tượng task với tên mới
            $item->image = $fileExtension;
        }
        try {
            $item->save();
            toast('Thêm sản phẩm thành công!','success','top-right');
            return redirect()->route('product.index');
        } catch (\Exception $th) {
            toast('Lối logic!','error','top-right');
            $image = 'public/images/'.$item->image;
            Storage::delete($image);
            return view('admin.products.add', $param);
            // return redirect()->route('product.add');
        }

    }

    public function show($id)
    {
        $users=User::all();
        $item=Product::find($id);
        return view('admin.products.detail', compact('item', 'users'));
    }

    public function edit($id)
    {
        $items = Category::all();
        $item=Product::find($id);
        return view('admin.products.edit', compact('item', 'items'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $item=Product::find($id);
        $item->name = $request->name;
        $item->age = $request->age;
        $item->color = $request->color;
        $item->gender = $request->gender;
        $item->price = $request->price;
        $item->quantity = $request->quantity;
        $item->category_id = $request->category_id;

        $item->user_id_edit=Auth()->user()->id;

        $file = $request->inputFile;
        // dd($request);

        if ($request->hasFile('inputFile')) {
            // dd($item->image);
            $image = 'public/images/'.$item->image;
            Storage::delete($image);

            $fileExtension = $file->getClientOriginalName();
            //Lưu file vào thư mục storage/app/public/image với tên mới
            $request->file('inputFile')->storeAs('public/images', $fileExtension);
            // Gán trường image của đối tượng task với tên mới
            $item->image = $fileExtension;
        }

        try {
            $item->save();
            // Storage::delete($image);
            toast('Sửa sản phẩm '.$item->name.' thành công!','success','top-right');
            return redirect()->route('product.index');
        } catch (\Exception $th) {
            toast('Lối logic!','error','top-right');
            $image = 'public/images/'.$fileExtension;
            Storage::delete($image);
            return redirect()->route('product.edit',$item->id);
        }
    }

    public function destroy($id)
    {
        $item=Product::findOrFail($id);
        try {
            $item->delete();
            // $image = 'public/images/'.$item->image;
            // Storage::delete($image);
            Alert::success('Xóa sản phẩm '.$item->name.' thành công');
            return redirect()->route('product.index');
        } catch (\Exception $th) {
            Alert::error('Xóa sản phẩm '.$item->name.' không thành công');
            return redirect()->route('product.index');
        }
    }

    public function softdelete()
    {
        $items=Product::onlyTrashed()->paginate(5);
        // dd($items);
        return view('admin.products.recycle', compact('items'));
    }

    public function retrieve($id)
    {
        $item=Product::withTrashed()->where('id', $id);
        try {
            $item->restore();
            $item=Product::find($id);
            toast('khôi phục sản phẩm '.$item->name.' thành công!','success','top-right');
            return redirect()->route('product.index');
        } catch (\Exception $th) {
            toast('khôi phục sản phẩm '.$item->name.' không thành công!','error','top-right');
            return redirect()->route('product.softdelete');
        }
    }
    public function deleted($id)
    {
        $item=Product::onlyTrashed()->findOrFail($id);
        try {
            $image = 'public/images/'.$item->image;
            Storage::delete($image);
            $item->forceDelete();
            Alert::success('Xóa sản phẩm '.$item->name.' thành công');
            return redirect()->route('product.softdelete');
        } catch (\Exception $th) {
            Alert::error('Xóa sản phẩm '.$item->name.' không thành công');
            return redirect()->route('product.softdelete');
        }
    }
}
