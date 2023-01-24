<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemsRequest;
use App\Http\Requests\UpdateItemsRequest;
use App\Models\Items;
use Inertia\Inertia;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Items/Index',[
          'items' => Items::select('id','name','price','is_selling')
          ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreItemsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemsRequest $request)
    {
        Items::create([
          'name' => $request->name,
          'memo' => $request->memo,
          'price' => $request->price
        ]);
        return to_route('items.index')->with([
          "message" => "登録が完了しました",
          "status" => "success",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function show(Items $item)
    {
        return Inertia::render('Items/Show',[
          "item" => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function edit(Items $item)
    {
      return Inertia::render('Items/Edit',[
        "item" => $item
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateItemsRequest  $request
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateItemsRequest $request, Items $item)
    {
        // dd($item->name,$request->name);
        $item->name = $request->name;
        $item->memo = $request->memo;
        $item->price = $request->price;
        $item->is_selling = $request->is_selling;
        $item->save();

        return to_route('items.index')->with([
          "message" => "更新しました",
          "status" => "success"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function destroy(Items $item)
    {
      $item->delete();

      return to_route('items.index')->with([
        "message" => '削除しました',
        "status" => "danger"
      ]);
    }
}
