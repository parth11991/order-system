<?php

namespace App\Http\Controllers\Admin;

use App\item;
use App\User;
use App\Supplier_has_item;

use App\orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreitemRequest;
use App\Http\Requests\UpdateitemRequest;
use App\Traits\UploadTrait;
use App\Notifications\packingwavesCompletedNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;
use Onfuro\Linnworks\Linnworks as Linnworks_API;
use Carbon\Carbon;
use Notification;
use Redirect;

class ItemController extends Controller
{

    use UploadTrait;

    /** @var Client  */
    protected $client;

    /** @var MockHandler  */
    protected $mock;

    /** @var array  */
    //protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock = new MockHandler([]);

        $this->mock->append(new Response(200, [],
            file_get_contents(__DIR__.'/stubs/AuthorizeByApplication.json')));

        $handlerStack = HandlerStack::create($this->mock);

        $this->client = new Client(['handler' => $handlerStack]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.items.index');
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function datatables(Request $request)
    {

        if ($request->ajax() == true) {

            $data = item::with([
                                    'users'
                                ]);
                                    //->get();
                                    //dd($data);
            
            return Datatables::eloquent($data)
                ->addColumn('users_avatars', function ($data) {
                    $users='<div class="avatars_overlapping">';
  
                    foreach ($data->users->reverse() as $key => $value) {
                       $users.='<span class="avatar_overlapping"><p tooltip="'.$value->name.'" flow="up"><img src="'.$value->getImageUrlAttribute($value->id).'" width="50" height="50" /></p></span>';
                    }

                    return $users.='</div>';
                })
                
                ->addColumn('action', function ($data) {
                    
                    $html='';
                    //if (auth()->user()->can('edit Item')){
                        $html.= '<a href="'.  route('admin.item.edit', ['item' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-1"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    //}

                    return $html; 
                
                })

                ->addColumn('item_img', function ($data) {
                    $path = $data->image;
                    if(empty($path)){
                        $path = asset('public/image/no_image.jpg');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $imagedata = @file_get_contents($path);
                    }else{
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $imagedata = @file_get_contents($path);
                    }

                    
                    if (strpos($imagedata[0], "200")) { 
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagedata);
                        return '<img src="'.$base64.'" alt="Item Image" class="profile-user-img-small img-fluid" style="width: 70px;height: 60px;">';
                    } else { 
                        $path = asset('public/image/no_image.jpg');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $imagedata = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagedata);
                        return '<img src="'.$base64.'" alt="Item Image" class="profile-user-img-small img-fluid" style="width: 70px;height: 60px;">';
                    } 
                    
                })

                ->rawColumns(['users_avatars', 'action', 'item_img'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = User::role('supplier')->get(); 
        return view('admin.items.create',compact('suppliers'));
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function search_suppliers(Request $request)
    {
        $keyWord = $request->keyword;
        $items = item::where('item_id',$keyWord)->with(['users'])->first();
        
        if(isset($items->users)){
            $user_arr = $items->users->pluck('id')->toArray();
        }else{
            $user_arr = array();
        }
        
        $suppliers = User::role('supplier')->get(); 

        $html='<div class="form-group">
                    <label>Supplier &nbsp;</label>
                    <select class="form-control select2" id="user_id" name="user_id" required autocomplete="user_id"><option value=""></option>';
                            foreach ($suppliers as $supplier) {
                                if(!in_array($supplier->id, $user_arr)){
                                    $html.='<option value="'.$supplier->id.'">'.$supplier->name.'</option>';
                                }
                            }
                    $html.='</select>
                </div>';

        return $html;
        
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function search_items(Request $request)
    {
        $keyWord = $request->keyword;

        $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);

        $getStockItems = $linnworks->Stock()->getStockItems($keyWord,"",5000,1,true,true,true);
        $html='<div class="form-group">
                    <label>Select Item</label>
                    <select class="form-control select2" id="item" name="item" required autocomplete="name"  onchange="funSearchSuppliers()"><option value=""></option>';
                            foreach ($getStockItems['Data'] as $item) {
                                $html.='<option value="'.$item['StockItemId'].'">'.$item['ItemNumber'].'-'.$item['ItemTitle'].'</option>';
                            }
                    $html.='</select>
                </div>';

        return $html;
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreitemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreitemRequest $request)
    {
        try {
            $StockItemId = $request->item;
            $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);

            $itemData = $linnworks->Inventory()->GetInventoryItemById($StockItemId);
            $itemImage = $linnworks->Inventory()->GetInventoryItemImages($StockItemId);
            $item = item::where('item_id',$StockItemId)->with(['users'])->first();
        
            if(isset($item)){
                if(count($item->users)==0 || !in_array($request->user_id, $item->users->pluck('id')->toArray())){
                    $item->users()->attach($request->user_id,
                                            ['product_weight' => $request->product_weight,
                                             'product_width' => $request->product_width,
                                             'product_length' => $request->product_length,
                                             'product_depth' => $request->product_depth,
                                             'box_inner_quantity' => $request->box_inner_quantity,
                                             'box_outer_quantity' => $request->box_outer_quantity,
                                             'box_weight_net_kg' => $request->box_weight_net_kg,
                                             'box_weight_gross_kg' => $request->box_weight_gross_kg,
                                             'box_width_cm' => $request->box_width_cm,
                                             'box_length_cm' => $request->box_length_cm,
                                             'box_depth_cm' => $request->box_depth_cm,
                                             'supplier_code' => $request->supplier_code,
                                             'supplier_barcode' => $request->supplier_barcode,
                                             'lead_time' => $request->lead_time,
                                             'supplier_price' => $request->supplier_price,
                                             'supplier_currency' => $request->supplier_currency,
                                             'min_order_quantity' => $request->min_order_quantity,
                                            ]);
                }
            }else{
                $item = new item();
                $item->item_id = $StockItemId;
                $item->sku = $itemData['ItemNumber'];
                $item->title = $itemData['ItemTitle'];
                if(isset($itemImage[0]['FullSource'])){
                  $item->image = $itemImage[0]['FullSource'];  
                }else{
                  $item->image = asset("/public/image/no_image.jpg"); 
                }
                $item->save();

                $item->users()->attach($request->user_id,
                                            ['product_weight' => $request->product_weight,
                                             'product_width' => $request->product_width,
                                             'product_length' => $request->product_length,
                                             'product_depth' => $request->product_depth,
                                             'box_inner_quantity' => $request->box_inner_quantity,
                                             'box_outer_quantity' => $request->box_outer_quantity,
                                             'box_weight_net_kg' => $request->box_weight_net_kg,
                                             'box_weight_gross_kg' => $request->box_weight_gross_kg,
                                             'box_width_cm' => $request->box_width_cm,
                                             'box_length_cm' => $request->box_length_cm,
                                             'box_depth_cm' => $request->box_depth_cm,
                                             'supplier_code' => $request->supplier_code,
                                             'supplier_barcode' => $request->supplier_barcode,
                                             'lead_time' => $request->lead_time,
                                             'supplier_price' => $request->supplier_price,
                                             'supplier_currency' => $request->supplier_currency,
                                             'min_order_quantity' => $request->min_order_quantity,
                                            ]);
            }

            //Session::flash('success', 'Item was created successfully.');
            //return redirect()->route('item.index');

            return response()->json([
                'success' => 'Item was created successfully.' // for status 200
            ]);

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
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(item $item)
    {

        $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);
        $itemData = $linnworks->Inventory()->GetInventoryItemById($item->item_id);
        $itemImage = $linnworks->Inventory()->GetInventoryItemImages($item->item_id);
        return view('admin.items.edit', compact("item","itemData","itemImage"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit_supplier(item $item,User $user)
    {
        $supplier_item_dimensions = Supplier_has_item::where('item_id',$item->id)->where('user_id',$user->id)->first();  
        return view('admin.items.edit_supplier', compact("item","user","supplier_item_dimensions"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateitemRequest  $request
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier_has_item $Supplier_has_item)
    {
        try {

            if (empty($Supplier_has_item)) {
                //Session::flash('failed', 'item Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'item update denied.' // for status 200
                ]);   
            }
                
            $Supplier_has_item->product_weight = $request->product_weight;
            $Supplier_has_item->product_width = $request->product_width;
            $Supplier_has_item->product_length = $request->product_length;
            $Supplier_has_item->product_depth = $request->product_depth;
            $Supplier_has_item->box_inner_quantity = $request->box_inner_quantity;
            $Supplier_has_item->box_outer_quantity = $request->box_outer_quantity;
            $Supplier_has_item->box_weight_net_kg = $request->box_weight_net_kg;
            $Supplier_has_item->box_weight_gross_kg = $request->box_weight_gross_kg;
            $Supplier_has_item->box_width_cm = $request->box_width_cm;
            $Supplier_has_item->box_length_cm = $request->box_length_cm;
            $Supplier_has_item->box_depth_cm = $request->box_depth_cm;
            $Supplier_has_item->supplier_code = $request->supplier_code;
            $Supplier_has_item->supplier_barcode = $request->supplier_barcode;
            $Supplier_has_item->lead_time = $request->lead_time;
            $Supplier_has_item->supplier_price = $request->supplier_price;
            $Supplier_has_item->supplier_currency = $request->supplier_currency;
            $Supplier_has_item->min_order_quantity = $request->min_order_quantity;
            $Supplier_has_item->save();

            return response()->json([
                'success' => 'item update successfully.' // for status 200
            ]);

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
     * Remove the specified resource from storage.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(item $item)
    {
        // delete item
        $item->delete();

        //return redirect('admin/item')->with('delete', 'item deleted successfully.');
        return response()->json([
            'delete' => 'item deleted successfully.' // for status 200
        ]);
    }
}
