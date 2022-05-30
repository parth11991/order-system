<?php

namespace App\Http\Controllers\Admin;

use App\item;
use App\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreitemRequest;
use App\Http\Requests\UpdateitemRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

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

            $data = item::select([
                                        'id',
                                        'title',
                                        'recurring',
                                        'due_date',
                                        'created_at',
                                        'updated_at',
                                    ])

                            ->with([
                                    'users',
                                    'Tasks_has_taskstatus' => function ($query) { 
                                            $query->orderBy('created_at', 'desc')
                                                    ->whereDate('created_at', Carbon::today())
                                                    ->with('creator','taskStatus');
                                            },
                                    'taskStatus' => function ($query) { 
                                            $query->orderBy('pivot_created_at', 'desc')->first();
                                    }
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
                ->addColumn('due_date_status', function ($data) {
                    if($data->recurring=='Daily'){
                        $due_date_status = Carbon::now()->format('Y-m-d');
                    }else{
                        $due_date_status = $data->due_date;
                    }
                
                    return $due_date_status;
                    
                })
                ->addColumn('taskAccepted', function ($data) {
                    if(isset($data->Tasks_has_taskstatus[0]) && isset($data->Tasks_has_taskstatus[0]->creator)){
                        if($data->Tasks_has_taskstatus[0]->taskstatuses_id==1){
                            return $taskAccepted = 'Not Accepted';
                        }
                        $taskAccepted = '<img src="'.$data->Tasks_has_taskstatus[0]->creator->getImageUrlAttribute($data->Tasks_has_taskstatus[0]->creator->id).'" alt="user_id_'.$data->Tasks_has_taskstatus[0]->creator->id.'" class="profile-user-img-small img-circle"> '. $data->Tasks_has_taskstatus[0]->creator->name;
                        
                    }else{
                        $taskAccepted = 'Not Accepted';
                    }
                
                    return $taskAccepted;
                    
                })
                ->addColumn('status', function ($data) {
                        $class ='text-danger';
                        $currentStatusID = 1;
                        if(isset($data->Tasks_has_taskstatus[0]) && isset($data->Tasks_has_taskstatus[0]->taskStatus)){
                            $taskActiveStatus = $data->Tasks_has_taskstatus[0]->taskStatus->name;
                            $currentStatusID = $data->Tasks_has_taskstatus[0]->taskStatus->id;
                            $class =$data->Tasks_has_taskstatus[0]->taskStatus->class;
                        }else{
                            $taskActiveStatus = 'Unaccepted';
                        }

                        $allTaskStatus = taskStatus::all();
                        $Status= '<div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o '.$class.'"></i> '.$taskActiveStatus.' </a><div class="dropdown-menu dropdown-menu-right" style="">';

                        foreach ($allTaskStatus as $allTaskStatus) {
                            $Status.= '<a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.','.$allTaskStatus->id.','.$currentStatusID.'); return false;"><i class="fa fa-dot-circle-o '.$allTaskStatus->class.'"></i> '.$allTaskStatus->name.'</a>';
                        }
                        
                        $Status.= '</div></div>';
                        return $Status;
                    })
                ->addColumn('action', function ($data) {
                    
                    $html='';
                    if (auth()->user()->can('edit Task')){
                        $html.= '<a href="'.  route('admin.task.edit', ['task' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-1"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    }

                    if (auth()->user()->can('delete Task')){
                        $html.= '<form method="post" class="float-left delete-formleft mr-1" action="'.  route('admin.task.destroy', ['task' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                    }

                    if (auth()->user()->can('view Task')){
                        $html.= '<a href="'.  route('admin.task.show', ['task' => $data->id]) .'" id="popup-modal-button"  class="btn btn-danger btn-sm"><span tooltip="View" flow="up"><i class="fas fa-eye"></i></span></a>';
                    }

                    return $html; 
                
                })
                ->rawColumns(['users_avatars', 'due_date_status', 'action', 'taskAccepted','status'])
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreitemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreitemRequest $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(item $item)
    {
        //
    }
}
