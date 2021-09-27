<?php

namespace App\Http\Controllers\API;

use App\Linnworks;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Onfuro\Linnworks\Api\Auth;
use Onfuro\Linnworks\Linnworks as Linnworks_API;

class LinnworksController extends Controller
{
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
    public function index(Request $request)
    {
        /*$validated = $request->validate([
            'token' => 'required',
            'tracking' => 'required|unique:linnworks,passportAccessToken',
        ]);*/

        $linnworks_api = Linnworks_API::make([
                            'applicationId' => env('LINNWORKS_APP_ID'),
                            'applicationSecret' => env('LINNWORKS_SECRET'),
                            'token' => $request->token,
                        ], $this->client);

        $user = auth()->user()->id;
        $linnworks = new Linnworks();
        $linnworks->token = $request->token;
        $linnworks->passportAccessToken = $request->tracking;
        $linnworks->applicationId = env('LINNWORKS_APP_ID');
        $linnworks->applicationSecret = env('LINNWORKS_SECRET');
        $linnworks->user_id = $user;
        $linnworks->linnworks_user_id = $linnworks_api->response()['UserId'];
        $linnworks->linnworks_email = $linnworks_api->response()['Email'];
        $linnworks->created_by = $user;
        $linnworks->updated_by = $user;
        $linnworks->save();
        
        return $request->token;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Linnworks  $linnworks
     * @return \Illuminate\Http\Response
     */
    public function show(Linnworks $linnworks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Linnworks  $linnworks
     * @return \Illuminate\Http\Response
     */
    public function edit(Linnworks $linnworks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Linnworks  $linnworks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Linnworks $linnworks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Linnworks  $linnworks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Linnworks $linnworks)
    {
        //
    }
}
