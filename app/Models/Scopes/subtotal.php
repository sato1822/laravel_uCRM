<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class subtotal implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
      $sql = 'select purchases.id as id
      , items.id as item_id
      , items_purchase.id as pivot_id
      , items.price * items_purchase.quantity as subtotal
      , customers.id as customer_id
      , customers.name as customer_name
      , items.name as item_name
      , items.price as item_price
      , items_purchase.quantity
      , purchases.status
      , purchases.created_at 
      , purchases.updated_at 
      from purchases
      left join items_purchase on purchases.id = items_purchase.purchase_id
      left join items on items_purchase.items_id = items.id 
      left join customers on purchases.customer_id = customers.id 
      ';

      $builder->fromSub($sql, 'order_subtotals');
    }
}
