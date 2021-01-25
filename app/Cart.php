<?php

namespace App;

class Cart{
    public $items = null;
    public $totalQty = 0;
    public $totalPrice = 0;
    public $itemToReplace;
    public $filter = [];
    public $checkout = null;

    public function __construct($oldCart){
        if ($oldCart) {
            $this->items = $oldCart->items;
            $this->totalQty = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
            $this->itemToReplace = $oldCart->itemToReplace;
            $this->checkout = null;
        }
    }
    public function addToCart($item , $product)
    {
        $storedItem = [
            'qty' => 0, 
            'price' => $item->price, 
            'item' => $item, 
            'id' => $product['id'], 
        ];
        if ($this->items) {
            if (array_key_exists($product['seller_id'], $this->items)) {
                $newSeller = false;
                foreach ($this->items[$product['seller_id']]['products'] as $key => $element) {
                    if ($element['id'] == $product['id']) {
                        $newProduct = false;
                        $storedItem = $this->items[$product['seller_id']]['products'][$key];
                        $this->itemToReplace = $key;
                        if ($product['qty'] != $storedItem['qty']) {
                            $formerPrice = $storedItem['price'];
                            $formerQty = $storedItem['qty'];
                            $newQty = true;
                        }else{
                            $newQty = false;
                        }
                    }else{
                        $newProduct = true;
                    }
                }
            }else{
                $newSeller = true;
            }
        }else{
            $newSeller = true;
        }
        $storedItem['qty'] = $product['qty'];
        $storedItem['price'] = $item->price * $product['qty'];
        if (isset($newQty)) {
            if($newQty == true){
                $this->totalQty = $this->totalQty - $formerQty + $product['qty'];
                $this->totalPrice = $this->totalPrice - $formerPrice +  ($item->price * $product['qty']);
            }
        }else{
            $this->totalQty += $product['qty'];
            $this->totalPrice += $storedItem['price'];
        }
        if (isset($newSeller)) {
            if ($newSeller == true) {
                $this->items[$product['seller_id']]['products'] = [];
                $this->items[$product['seller_id']]['seller_id'] = $product['seller_id'];
                $this->items[$product['seller_id']]['seller_image'] = $product['seller_image'];
                $this->items[$product['seller_id']]['seller_name'] = $product['seller_name'];
                $this->items[$product['seller_id']]['seller_location'] = $product['seller_location'];
                $this->items[$product['seller_id']]['seller_telephone'] = $product['seller_telephone'];
                $this->items[$product['seller_id']]['created_at'] = date('Y-m-d h:i:s');
                array_push($this->items[$product['seller_id']]['products'], $storedItem);
            }else{
                if ($newProduct) {
                    array_push($this->items[$product['seller_id']]['products'], $storedItem);
                }else{
                    $this->items[$product['seller_id']]['products'][$this->itemToReplace] = $storedItem;
                }
            }
        }
    }

    public function removeFromCart($id)
    {
        # code...
        unset($this->items[$id]);
    }

    public function filterCart()
    {
        foreach ($this->items as $key => $item) {
            $this->filter[strtotime($item['created_at'])] = $item; 
        }
        krsort($this->filter);
    }

    public function removeSeller($sid)
    {
        $qty = 0;
        $price = 0;
        if ($this->items) {
            if (array_key_exists($sid, $this->items)) {
                foreach ($this->items[$sid]['products'] as $key => $element) {
                    $qty += $element['qty']; 
                    $price += $element['price']; 
                }
            }
        }   
        $this->totalQty = $this->totalQty - $qty;
        $this->totalPrice = $this->totalPrice - $price;
        unset($this->items[$sid]);
    }

    public function removeProduct($id, $sid)
    {
        $qty = 0;
        $price = 0;
        if ($this->items) {
            if (array_key_exists($sid, $this->items)) {
                foreach ($this->items[$sid]['products'] as $key => $element) {
                    if ($element['id'] == $id) {
                        $qty += $element['qty']; 
                        $price += $element['price']; 
                        unset($this->items[$sid]['products'][$key]);
                    }
                }
            }
        }   
        $this->totalQty = $this->totalQty - $qty;
        $this->totalPrice = $this->totalPrice - $price;
        if(count($this->items[$sid]['products']) <= 0){
            unset($this->items[$sid]);
        }
    }

    public function contactSeller($sid)
    {
        $qty = 0;
        $price = 0;
        $products = [];
        if ($this->items) {
            if (array_key_exists($sid, $this->items)) {
                foreach ($this->items[$sid]['products'] as $key => $element) {
                    $qty += $element['qty']; 
                    $price += $element['price']; 
                    $products[$key] = $element;
                }
            }
        } 
        $this->totalQty = $this->totalQty - $qty;
        $this->totalPrice = $this->totalPrice - $price;
        $this->checkout = $products;
        unset($this->items[$sid]);
    }
}