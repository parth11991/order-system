<?php

namespace App\Http\Controllers\Admin;

use App\OrdersFiles;
use App\Http\Requests\StoreOrdersFilesRequest;
use App\Http\Requests\UpdateOrdersFilesRequest;

use App\Http\Controllers\Controller;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use File;

class OrdersFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = $request->type;
        $order_id = $request->order;
        return view('admin.order-file.create', compact("order_id","type"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrdersFilesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrdersFilesRequest $request)
    {
        try {

            if($request->hasfile('order_files'))
            {
                foreach($request->file('order_files') as $file)
                {
                    $Size = $file->getSize();
                    $type = $file->extension();
                    $file_name = time().rand(1,100).'.'.$type;
                    $file->move(public_path('order_files'), $file_name);  
                    $OrderFiles = new OrderFiles();
                    $OrderFiles->file_name = $file_name;
                    $OrderFiles->originalname = $file->getClientOriginalName();
                    $OrderFiles->size = $Size;
                    $OrderFiles->type = $type;
                    $OrderFiles->order_id = $request->order_id;
                    $OrderFiles->created_by = auth()->user()->id;
                    $OrderFiles->updated_by = auth()->user()->id;
                    $OrderFiles->save();
                }
            }
            
            Session::flash('success', 'order files were uploaded successfully.');
            return redirect()->route('admin.order.show', ['order' => $request->order_id]);

            /*return response()->json([
                'success' => 'Project was created successfully.' // for status 200
            ]);*/

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OrdersFiles  $ordersFiles
     * @return \Illuminate\Http\Response
     */
    public function show(OrdersFiles $ordersFiles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OrdersFiles  $ordersFiles
     * @return \Illuminate\Http\Response
     */
    public function edit(OrdersFiles $ordersFiles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrdersFilesRequest  $request
     * @param  \App\OrdersFiles  $ordersFiles
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrdersFilesRequest $request, OrdersFiles $ordersFiles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OrdersFiles  $ordersFiles
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrdersFiles $order_file)
    {
        File::delete(public_path('project_files/').$order_file->file_name);
        $order_file->delete();

        Session::flash('success', 'Order file deleted successfully.');
        return redirect()->route('admin.order.show', ['order' => $request->order]);
    }
}
