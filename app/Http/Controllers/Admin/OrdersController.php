<?php

namespace App\Http\Controllers\Admin;

use App\orders;
use App\Company;
use App\User;
use App\item;
use App\Supplier_has_item;
use App\OrdersFiles;

use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Hash;
use File;

class OrdersController extends Controller
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

    function __construct()
    {

        $this->middleware('can:create order', ['only' => ['create', 'store']]);
        $this->middleware('can:edit order', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete order', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.orders.index');
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

            $user_id = auth()->user()->id;

            $search = "%{$request->search['value']}%";

            $constraint = function ($query) use ($search){
                $query->where('name', 'like', $search);
            };

            if(auth()->user()->hasRole('superadmin')){
                $model = orders::with(['creator','company','supplier']);
            }elseif(auth()->user()->hasRole('admin')){
                $model = orders::with(['creator','company','supplier']);
            }else{
                $model = orders::with(['creator','company','supplier'])->where('created_by', $user_id);
            }

            if($search!="%%"){
                $model =  $model->whereHas('company', $constraint)
                                ->orWhereHas('supplier', $constraint)
                                ->orWhere(function($query) use ($search) {
                                    return $query->where('item_title', 'like', $search)
                                    ->orWhere('sku', 'like', $search);
                                 }); 
            }
            

            return Datatables::eloquent($model)
                ->addColumn('action', function (orders $data) {
                    $html='';
                    if (auth()->user()->can('edit order')){
                        $html.= '<a href="'.  route('admin.order.edit', ['order' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    }

                    if (auth()->user()->can('delete order')){
                        $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.order.destroy', ['order' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                    }

                    return $html; 
                })

                ->addColumn('order_status', function ($data) {
                    if($data->status=='0'){ 
                        $class ='text-danger';    
                        $status= 'new order';
                    }elseif ($data->status=='1') {
                        $class= 'text-warning';
                        $status= 'confirmed';
                    }elseif ($data->status=='2') {
                        $class= 'text-info';
                        $status= 'shipped';
                    }elseif ($data->status=='3') {
                        $class ='text-success';
                        $status= 'received';
                    }elseif ($data->status=='4') {
                        $class ='text-secondary';
                        $status= 'quote';
                    }else{
                        $class ='text-primary';
                        $status= 'approved';
                    }

                    return '<div class="dropdown action-label">
                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o '.$class.'"></i> '.$status.' </a>
                            <div class="dropdown-menu dropdown-menu-right" style="" class="noExport">
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',4); return false;"><i class="fa fa-dot-circle-o text-secondary"></i> quote</a>
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',5); return false;"><i class="fa fa-dot-circle-o text-primary"></i> approved</a>
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',0); return false;"><i class="fa fa-dot-circle-o text-danger"></i> new order</a>
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',1); return false;"><i class="fa fa-dot-circle-o text-warning"></i> confirmed</a>
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',2); return false;"><i class="fa fa-dot-circle-o text-info"></i> shipped</a>
                                <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',3); return false;"><i class="fa fa-dot-circle-o text-success"></i> received</a>
                            </div>
                        </div>';
                })

                ->addColumn('status_field', function ($data) {
                    if($data->status=='0'){    
                        $status= 'new order';
                    }elseif ($data->status=='1') {
                        $status= 'confirmed';
                    }elseif ($data->status=='2') {
                        $status= 'shipped';
                    }elseif ($data->status=='3') {
                        $status= 'received';
                    }elseif ($data->status=='4') {
                        $status= 'quote';
                    }else{
                        $status= 'approved';
                    }

                    return $status;
                })

                ->addColumn('order_date', function ($data) {
                    return Carbon::parse($data->created_at);
                })

                ->addColumn('company_name', function ($data) {
                    if(isset($data->company->name)){
                        return $data->company->name;
                    }
                })

                ->addColumn('supplier_name', function ($data) {
                    if(isset($data->supplier->name)){
                        return $data->supplier->name;
                    }
                })

                ->addColumn('item_img', function ($data) {
                    $path = $data->image;
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $imagedata = @file_get_contents($path);
                    if (strpos($http_response_header[0], "200")) { 
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

                ->rawColumns(['item_img','company_name','order_date','order_status','action','status_field','supplier_name'])

                ->make(true);
        }
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function datatables_supplier(Request $request)
    {

        if ($request->ajax() == true) {

            $user_id = auth()->user()->id;
            $search = "%{$request->search['value']}%";

            $model = orders::where('supplier_id', $user_id)->with(['creator','company','supplier']);

            if($search!="%%"){

                /*$constraint = function ($query) use ($search){
                    $query->where('name', 'like', $search);
                };

                $model = $model->where(function($query) use ($search) {
                                    return $query->whereHas('company', function ($query) use ($search) {
                                                    $query->orWhere('name', 'like', $search);
                                                })->orWhere('item_title', 'like', $search)
                                                ->orWhere('sku', 'like', $search);
                                 });*/


                $model = $model->whereHas('company', function($q) use($search)
                {
                    $q->where('name', 'like', $search);
                })
                ->orWhere('item_title', 'like', $search)
                ->orWhere('sku', 'like', $search);
            }

            return Datatables::eloquent($model)
                    ->addColumn('action', function (orders $data) {
                        $html='';
                        if (auth()->user()->can('edit order')){
                            $html.= '<a href="'.  route('admin.order.edit_supplier', ['order' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        /*if (auth()->user()->can('delete order')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.order.destroy', ['order' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }*/

                        return $html; 
                    })

                    ->addColumn('company_name', function ($data) {
                        if(isset($data->company->name)){
                            return $data->company->name;
                        }
                    })

                    ->addColumn('order_status', function ($data) {
                        if($data->status=='0'){ 
                            $class ='text-danger';    
                            $status= 'new order';
                        }elseif ($data->status=='1') {
                            $class= 'text-warning';
                            $status= 'confirmed';
                        }elseif ($data->status=='2') {
                            $class= 'text-info';
                            $status= 'shipped';
                        }elseif ($data->status=='3') {
                            $class ='text-success';
                            $status= 'received';
                        }elseif ($data->status=='4') {
                            $class ='text-secondary';
                            $status= 'quote';
                        }else{
                            $class ='text-primary';
                            $status= 'approved';
                        }

                        $received = '';
                        if(!auth()->user()->hasRole('supplier')){
                           $received .= '<a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',3); return false;"><i class="fa fa-dot-circle-o text-success"></i> received</a>'; 
                        }
                        

                        return '<div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o '.$class.'"></i> '.$status.' </a>
                                <div class="dropdown-menu dropdown-menu-right" style="">
                                    
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',4); return false;"><i class="fa fa-dot-circle-o text-secondary"></i> quote</a>
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',5); return false;"><i class="fa fa-dot-circle-o text-primary"></i> approved</a>
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',0); return false;"><i class="fa fa-dot-circle-o text-danger"></i> new order</a>
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',1); return false;"><i class="fa fa-dot-circle-o text-warning"></i> confirmed</a>
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',2); return false;"><i class="fa fa-dot-circle-o text-info"></i> shipped</a>
                                    '.$received.'
                                    
                                </div>

                                <a href="'.  route('admin.order.edit_supplier', ['order' => $data->id]) .'" class="edit_'.$data->id.'"  id="popup-modal-button" style="display: none;"></a>
                            </div>';
                    })

                    ->addColumn('order_date', function ($data) {
                        return Carbon::parse($data->created_at);
                    })

                    ->addColumn('supplier_name', function ($data) {
                        if(isset($data->supplier->name)){
                            return $data->supplier->name;
                        }
                    })

                    ->addColumn('item_img', function ($data) {
                        $path = $data->image;
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $imagedata = @file_get_contents($path);
                        if (strpos($http_response_header[0], "200")) { 
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

                    ->addColumn('status_field', function ($data) {
                        if($data->status=='0'){    
                            $status= 'new order';
                        }elseif ($data->status=='1') {
                            $status= 'confirmed';
                        }elseif ($data->status=='2') {
                            $status= 'shipped';
                        }elseif ($data->status=='3') {
                            $status= 'received';
                        }elseif ($data->status=='4') {
                            $status= 'quote';
                        }else{
                            $status= 'approved';
                        }

                        return $status;
                    })

                    ->rawColumns(['item_img','company_name','order_date','order_status','action','status_field','supplier_name'])

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
        $companies = Company::all(); 
        return view('admin.orders.create',compact('suppliers','companies'));
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
        //dd(env('LINNWORKS_APP_ID'));
        $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);

        $getStockItems = $linnworks->Stock()->getStockItems($keyWord,'',5000,1,true,true,true);
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
            $suppliers = User::role('supplier')->orderByRaw("field(id,".implode(',',$user_arr).") desc")->get();
        }else{
            $user_arr = array();
            $suppliers = User::role('supplier')->get();
        }
        
         

        $html='<div class="form-group">
                    <label>Supplier &nbsp;</label>
                    <select class="form-control select2" id="supplier_id" name="supplier_id" required autocomplete="supplier_id" onchange="funGetSupplierItemDimensions(this.value)"><option value=""></option>';
                            foreach ($suppliers as $supplier) {
                                $html.='<option value="'.$supplier->id.'">'.$supplier->name.'</option>';
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
    public function get_supplier_item_dimensions(Request $request)
    {

        $item = item::where('item_id',$request->item)->first();
        $user_id = $request->user_id;
        $supplier_item_dimensions = Supplier_has_item::where('item_id',$item->id)->where('user_id',$user_id)->first();
        return response()->json($supplier_item_dimensions);
    }  


    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function latest_order(Request $request)
    {
        if ($request->ajax() == true) {

            $user_id = auth()->user()->id;
            $model = orders::where('item_id',$request->keyword)->with(['supplier','company']);

            return Datatables::eloquent($model)
                    
                    ->addColumn('order_status', function ($data) {
                        if($data->status=='0'){ 
                            $class ='text-danger';    
                            $status= 'new order';
                        }elseif ($data->status=='1') {
                            $class= 'text-warning';
                            $status= 'confirmed';
                        }elseif ($data->status=='2') {
                            $class= 'text-info';
                            $status= 'shipped';
                        }elseif ($data->status=='3') {
                            $class ='text-success';
                            $status= 'received';
                        }elseif ($data->status=='4') {
                            $class ='text-secondary';
                            $status= 'quote';
                        }else{
                            $class ='text-primary';
                            $status= 'approved';
                        }

                        return '<div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o '.$class.'"></i> '.$status.' </a>
                            </div>';
                    })

                    ->addColumn('order_date', function ($data) {
                        return Carbon::parse($data->created_at);
                    })

                    ->addColumn('company_name', function ($data) {
                        if(isset($data->company->name)){
                            return $data->company->name;
                        }
                    })
                    ->addColumn('item_img', function ($data) {
                        $path = $data->image;
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $imagedata = @file_get_contents($path);
                        if (strpos($http_response_header[0], "200")) { 
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

                    ->addColumn('supplier', function ($data) {
                        
                        return '<img src="'.$data->supplier->getImageUrlAttribute($data->supplier->id).'" alt="user_id_'.$data->supplier->id.'" class="profile-user-img-small img-circle"> '. $data->supplier->name;
                    })

                    ->rawColumns(['item_img','order_date','order_status','company_name','supplier'])

                    ->make(true);
        }
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $StockItemId = $request->item;

            $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);

            $itemData = $linnworks->Inventory()->GetInventoryItemById($StockItemId);
            $orders = new orders();
            $orders->item_id = $StockItemId;
            $orders->sku = $itemData['ItemNumber'];
            $orders->item_title = $itemData['ItemTitle'];

            $itemImage = $linnworks->Inventory()->GetInventoryItemImages($StockItemId);

            $propertyParams = [
                                "PropertyName"=> "MPN",
                                "PropertyType"=> "Attribute"
                            ];
            $itemExtendedProperties = $linnworks->Inventory()->GetInventoryItemExtendedProperties($StockItemId,$propertyParams);

            if(isset($itemExtendedProperties[0]['PropertyValue'])){
                $orders->customer_sku = $itemExtendedProperties[0]['PropertyValue'];
            }else{
                $orders->customer_sku = $itemData['ItemNumber'];
            }
            

            if(isset($itemImage[0]['FullSource'])){
              $orders->image = $itemImage[0]['FullSource'];  
            }else{
              $orders->image = asset("/public/image/no_image.jpg"); 
            }
            
            $orders->supplier_id = $request->supplier_id;
            $orders->company_id = $request->company_id;
            $orders->price = $request->price;
            $orders->new_price = $request->new_price;
            $orders->old_price = $request->old_price;
            $orders->currency = $request->currency;
            $orders->old_price_currency = $request->old_price_currency;
            $orders->new_price_currency = $request->new_price_currency;
            $orders->qty = $request->qty;
            $orders->notes = $request->notes;
            //$orders->due_date = Carbon::parse($request->due_date);
            $orders->created_by = auth()->user()->id;
            $orders->updated_by = auth()->user()->id;
            $orders->save();

            $item = item::where('item_id',$StockItemId)->with(['users'])->first();
        
            $supplier_item_dimensions = ['product_weight' => $request->product_weight,
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
                                         'supplier_price' => $request->price,
                                         'supplier_currency' => $request->currency,
                                        ];


            if(isset($item)){
                $Supplier_has_item = Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->count();
                if($Supplier_has_item>0){
                    Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->update($supplier_item_dimensions);
                }else{
                    $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
                }
                
            }else{
                $item = new item();
                $item->item_id = $StockItemId;
                if(isset($itemImage[0]['FullSource'])){
                  $item->image = $itemImage[0]['FullSource'];  
                }else{
                  $item->image = asset("/public/image/no_image.jpg"); 
                }
                $item->sku = $itemData['ItemNumber'];
                $item->title = $itemData['ItemTitle'];
                $item->save();

                $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
            }
            

            for ($x = 0; $x < $request->TotalFiles; $x++) 
            {
               if ($request->hasFile('files'.$x)) 
                {
                    $file      = $request->file('files'.$x);
                    $Size = $file->getSize();
                    $type = $file->extension();
                    $file_name = time().rand(1,100).'.'.$type;
                    $file->move(public_path('order_files'), $file_name);  
                    $OrdersFiles = new OrdersFiles();
                    $OrdersFiles->file_name = $file_name;
                    $OrdersFiles->originalname = $file->getClientOriginalName();
                    $OrdersFiles->size = $Size;
                    $OrdersFiles->type = $type;
                    $OrdersFiles->order_id = $orders->id;
                    $OrdersFiles->created_by = auth()->user()->id;
                    $OrdersFiles->updated_by = auth()->user()->id;
                    $OrdersFiles->save();
                }
            }

            /*Session::flash('success', 'order was created successfully.');
            return redirect()->back();*/

            return response()->json([
                'success' => 'Order was created successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {
            dd($exception);
            DB::rollBack();

            Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            return redirect()->back()->withInput($request->all());

            /*return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);*/
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(orders $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(orders $order)
    {
        $suppliers = User::role('supplier')->get(); 
        $companies = Company::all(); 
        return view('admin.orders.edit', compact("order",'suppliers','companies'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit_supplier(orders $order)
    {
        $suppliers = User::role('supplier')->get(); 
        $companies = Company::all(); 
        return view('admin.orders.edit_supplier', compact("order",'suppliers','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, orders $order)
    {
        try {

            if (empty($order)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'order update denied.' // for status 200
                ]);   
            }

            $StockItemId = $request->item;
            
            $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);
            $itemData = $linnworks->Inventory()->GetInventoryItemById($StockItemId);

            $order->item_id = $StockItemId;
            $order->sku = $itemData['ItemNumber'];
            $order->item_title = $itemData['ItemTitle'];

            $itemImage = $linnworks->Inventory()->GetInventoryItemImages($StockItemId);
            if(isset($itemImage[0]['FullSource'])){
              $order->image = $itemImage[0]['FullSource'];  
            }else{
              $order->image = asset("/public/image/no_image.jpg"); 
            }

            $propertyParams = [
                                "PropertyName"=> "MPN",
                                "PropertyType"=> "Attribute"
                            ];
            $itemExtendedProperties = $linnworks->Inventory()->GetInventoryItemExtendedProperties($StockItemId,$propertyParams);

            if(isset($itemExtendedProperties[0]['PropertyValue'])){
                $order->customer_sku = $itemExtendedProperties[0]['PropertyValue'];
            }else{
                $order->customer_sku = $itemData['ItemNumber'];
            }
            
            $order->supplier_id = $request->supplier_id;
            $order->company_id = $request->company_id;
            $order->price = $request->price;
            $order->new_price = $request->new_price;
            $order->old_price = $request->old_price;
            $order->currency = $request->currency;
            $order->old_price_currency = $request->old_price_currency;
            $order->new_price_currency = $request->new_price_currency;
            $order->qty = $request->qty;
            $order->due_date = Carbon::parse($request->due_date);
            $order->notes = $request->notes;
            $order->status = $request->status;
            $order->updated_by = auth()->user()->id;
            $order->save();

            $item = item::where('item_id',$StockItemId)->with(['users'])->first();
        
            $supplier_item_dimensions = ['product_weight' => $request->product_weight,
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
                                         'lead_time' => 0,
                                         'supplier_price' => $request->price,
                                         'supplier_currency' => $request->currency,
                                         'min_order_quantity' => 0,
                                        ];


            if(isset($item)){
                $Supplier_has_item = Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->count();
                if($Supplier_has_item>0){
                    Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->update($supplier_item_dimensions);
                }else{
                    $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
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

                $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
            }
            //Session::flash('success', 'A branch updated successfully.');
            //return redirect('admin/branch');

            return response()->json([
                'success' => 'order update successfully.' // for status 200
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update_supplier(Request $request, orders $order)
    {
        try {

            if (empty($order)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'order update denied.' // for status 200
                ]);   
            }

            $StockItemId = $request->item;
            $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);

            $itemData = $linnworks->Inventory()->GetInventoryItemById($StockItemId);

            $order->item_id = $StockItemId;
            $order->sku = $itemData['ItemNumber'];
            $order->item_title = $itemData['ItemTitle'];

            $itemImage = $linnworks->Inventory()->GetInventoryItemImages($StockItemId);
            if(isset($itemImage[0]['FullSource'])){
              $order->image = $itemImage[0]['FullSource'];  
            }else{
              $order->image = asset("/public/image/no_image.jpg"); 
            }

            $propertyParams = [
                                "PropertyName"=> "MPN",
                                "PropertyType"=> "Attribute"
                            ];
            $itemExtendedProperties = $linnworks->Inventory()->GetInventoryItemExtendedProperties($StockItemId,$propertyParams);

            if(isset($itemExtendedProperties[0]['PropertyValue'])){
                $order->customer_sku = $itemExtendedProperties[0]['PropertyValue'];
            }else{
                $order->customer_sku = $itemData['ItemNumber'];
            }
            
            /*$order->supplier_id = $request->supplier_id;
            $order->company_id = $request->company_id;*/
            $order->price = $request->price;
            $order->new_price = $request->new_price;
            $order->old_price = $request->old_price;
            $order->currency = $request->currency;
            $order->old_price_currency = $request->old_price_currency;
            $order->new_price_currency = $request->new_price_currency;
            $order->qty = $request->qty;
            $order->due_date = Carbon::parse($request->due_date);
            $order->status = $request->status;
            $order->notes = $request->notes;
            $order->updated_by = auth()->user()->id;
            //dd($order);
            $order->save();

            $item = item::where('item_id',$StockItemId)->with(['users'])->first();
            
            $supplier_item_dimensions = ['product_weight' => $request->product_weight,
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
                                         'lead_time' => 0,
                                         'supplier_price' => $request->price,
                                         'supplier_currency' => $request->currency,
                                         'min_order_quantity' => 0,
                                        ];


            if(isset($item)){
                $Supplier_has_item = Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->count();
                if($Supplier_has_item>0){
                    Supplier_has_item::where('item_id',$item->id)->where('user_id',$request->supplier_id)->update($supplier_item_dimensions);
                }else{
                    $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
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

                $item->users()->attach($request->supplier_id,$supplier_item_dimensions);
            }
            //Session::flash('success', 'A branch updated successfully.');
            //return redirect('admin/branch');

            return response()->json([
                'success' => 'order update successfully.' // for status 200
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
     * @param  \App\orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy(orders $order)
    {
        // delete branch
        $order->delete();

        //return redirect('admin/branch')->with('delete', 'branch deleted successfully.');
        return response()->json([
            'delete' => 'order deleted successfully.' // for status 200
        ]);
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function change_status(Request $request)
    {
        try {

            $order = orders::find($request->id);
            if (empty($order)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'order update denied.' // for status 200
                ]);   
            }

            $order->status = $request->status;
            $order->save();

            /*NOTIFICATION CREATE [START]*/
            /*if($old_status != $leave->status){
                
                $sender = User::find($leave->approved_by);
                $receiver = User::find($leave->employee_id);
                if($leave->status=='New'){$leave->status='on hold';}
                $leaveData = [
                    'name' => Str::lower($leave->status),
                    'subject' => 'Leave Notification' ,
                    'body' => 'your leave has been '.Str::lower($leave->status),
                    'thanks' => 'Thank you',
                    'leaveUrl' => url('admin/leave'),
                    'leave_id' => $leave->id,
                    'employee_id' => $sender->id,
                    'employee_name' => $sender->name,
                    'receiver_name' => $receiver->name,
                    'text' => 'your leave has been '.Str::lower($leave->status)
                ];

                Notification::send($receiver, new leavesNotification($leaveData));
                Mail::to($receiver->email)->send(new LeavesNotificationMail($leaveData));
                
            }*/
            /*NOTIFICATION CREATE [END]*/

            

            //Session::flash('success', 'A branch updated successfully.');
            //return redirect('admin/branch');

            return response()->json([
                'success' => 'order update successfully.' // for status 200
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


    
}
