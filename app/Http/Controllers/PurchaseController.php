<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Customer;
use App\Models\Items;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use JetBrains\PhpStorm\Pure;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Order::paginate(50));

        $orders = Order::groupBy('id')
        ->selectRaw('id,sum(subtotal) as total,
        customer_name, customer_id,status, created_at')
        ->paginate(50);

        // dd($orders);

      return Inertia::render('Purchases/Index', [
        'orders' => $orders
      ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $customers = Customer::select('id','name','kana')->get();
        $items = Items::select('id','name','price')
        ->where('is_selling', true)
        ->get();

        return Inertia::render('Purchases/Create', [
          // 'customers' => $customers,
          'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
        // dd($request);
        //保存中の処理に何か問題が発生した場合は途中で処理を中断できる

        DB::beginTransaction();
        try{
          $purchase = Purchase::create([
            'customer_id' => $request->customer_id,
            'status' => $request->status
          ]);

          foreach($request->items as $item){
            $purchase->items()->attach($purchase->id, [//attachとつけることにより中間のデータベースの値を作成することができる
              'items_id' => $item['id'],
              'quantity' => $item['quantity']
            ]);
          }
          DB::commit();

          return to_route('dashboard');
        } catch (\Exception $e){

          DB::rollBack();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //小計
        $items = Order::where('id', $purchase->id)->get();

        //合計
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, sum(subtotal) as total,
        customer_name, status, created_at, updated_at')
        ->get();
        // dd($item, $order);

        return Inertia::render('Purchases/Show', [
          'items' => $items,
          'order' => $order
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        $purchase = Purchase::find($purchase->id);

        $allItems = Items::select('id', 'name', 'price')
        ->get();

        $items = [];

        foreach($allItems as $allItem){
          $quantity = 0;
          foreach($purchase->items as $item){
            // dd($allItem, $item);
            if($allItem->id === $item->id){
              $quantity = $item->pivot->quantity;
              // dd($quantity);
            }
          }
          array_push($items, [
            "id" => $allItem->id,
            "name" => $allItem->name,
            "price" => $allItem->price,
            "quantity" => $quantity
          ]);
        }
        // dd($items);

        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, customer_id,
        customer_name, status, created_at, updated_at')
        ->get();

        return Inertia::render('Purchases/Edit', [
          'items' => $items,
          'order' => $order
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
      DB::beginTransaction();
      
      try{
        dd($purchase->items[0]->pivot->quantity);
        $purchase->status = $request->status;
          $purchase->save();
          
          $items = [];
          // dd($request->items);
  
          foreach($request->items as $item){
            $items = $items + [
              $item['id'] => [
                'quantity' => $item['quantity']
              ]
              ];
          }

          $purchase->items()->sync($items);
          DB::commit();

          return to_route('dashboard');
          
        } catch (\Exception $e){
        DB::rollBack();
        };
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
