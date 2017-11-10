<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Requests\StoreCharacter;
use App\Http\Requests\StoreItem;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ItemController extends Controller
{
    /**
     * @var string
     */
    protected $view = 'items';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('campaign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Item::with(['location', 'character'])
            ->search(request()->get('search'))
            ->order(request()->get('order'))
            ->paginate();
        return view($this->view . '.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Item::class);

        return view($this->view . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItem $request)
    {
        $this->authorize('create', Item::class);

        Item::create($request->all());
        if ($request->has('submit-new')) {
            return redirect()->route($this->view . '.create')
                ->with('success', trans($this->view . '.create.success'));
        }
        return redirect()->route($this->view . '.index')
            ->with('success', trans($this->view . '.create.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        return view($this->view . '.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        return view($this->view . '.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function update(StoreItem $request, Item $item)
    {
        $this->authorize('update', $item);

        $item->update($request->all());
        if ($request->has('submit-new')) {
            return redirect()->route($this->view . '.create')
                ->with('success', trans($this->view . '.edit.success'));
        }
        return redirect()->route($this->view . '.show', $item->id)
            ->with('success', trans($this->view . '.edit.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Character  $character
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $item->delete();
        return redirect()->route($this->view . '.index')
            ->with('success', trans($this->view . '.destroy.success'));
    }
}