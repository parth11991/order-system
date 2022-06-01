<?php

namespace App\Http\Controllers\Admin;

use App\item;
use App\User;

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
                        $html.= '<a href="'.  route('admin.item.edit', ['item' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-1"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    //}

                    return $html; 
                
                })
                ->rawColumns(['users_avatars', 'action'])
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
                    <select class="form-control select2" id="user_id" name="user_id[]" required autocomplete="user_id" multiple><option value=""></option>';
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

            $item = item::where('item_id',$StockItemId)->with(['users'])->first();
        
            if(isset($item)){
                if(count($item->users)==0 || !in_array($request->user_id, $item->users->pluck('id')->toArray())){
                    $item->users()->attach($request->user_id);
                }
            }else{
                $item = new item();
                $item->item_id = $StockItemId;
                $item->sku = $itemData['ItemNumber'];
                $item->title = $itemData['ItemTitle'];
                $item->save();

                $item->users()->attach($request->user_id);
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
        $suppliers = User::role('supplier')->get(); 
        return view('admin.orders.edit', compact("item",'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateitemRequest  $request
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateitemRequest $request, item $item)
    {
        try {

            if (empty($item)) {
                //Session::flash('failed', 'item Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'item update denied.' // for status 200
                ]);   
            }

            $StockItemId = $request->item;
            
            $linnworks = Linnworks_API::make([
                        'applicationId' => env('LINNWORKS_APP_ID'),
                        'applicationSecret' => env('LINNWORKS_SECRET'),
                        'token' => env('LINNWORKS_TOKEN'),
                    ], $this->client);
            $itemData = $linnworks->Inventory()->GetInventoryItemById($StockItemId);

            $item->item_id = $StockItemId;
            $item->sku = $itemData['ItemNumber'];
            $item->item_title = $itemData['ItemTitle'];
            $item->save();
            $item->users()->detach();

            $item->users()->syncWithoutDetaching($request->user_id);

            //Session::flash('success', 'A item updated successfully.');
            //return redirect('admin/item');

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
