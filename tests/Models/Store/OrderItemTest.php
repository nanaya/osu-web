<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Models\Store;

use App\Exceptions\InsufficientStockException;
use App\Models\Store\OrderItem;
use App\Models\Store\Product;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    public function testReserveUnreservedProduct()
    {
        $product = Product::factory()->create(['stock' => 5, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => false,
        ]);

        $orderItem->reserveProduct();

        $orderItem->refresh();
        $product->refresh();

        $this->assertTrue($orderItem->reserved);
        $this->assertSame($product->stock, 3);
    }

    public function testReserveReservedProduct()
    {
        $product = Product::factory()->create(['stock' => 5, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => true,
        ]);

        $orderItem->reserveProduct();

        $orderItem->refresh();
        $product->refresh();

        $this->assertTrue($orderItem->reserved);
        $this->assertSame($product->stock, 5);
    }

    public function testReleaseUnreservedProduct()
    {
        $product = Product::factory()->create(['stock' => 5, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => false,
        ]);

        $orderItem->releaseProduct();

        $orderItem->refresh();
        $product->refresh();

        $this->assertFalse($orderItem->reserved);
        $this->assertSame($product->stock, 5);
    }

    public function testReleaseReservedProduct()
    {
        $product = Product::factory()->create(['stock' => 5, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => true,
        ]);

        $orderItem->releaseProduct();

        $orderItem->refresh();
        $product->refresh();

        $this->assertFalse($orderItem->reserved);
        $this->assertSame($product->stock, 7);
    }

    public function testReserveInsufficientStock()
    {
        $product = Product::factory()->create(['stock' => 1, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => false,
        ]);

        $this->expectException(InsufficientStockException::class);
        $orderItem->reserveProduct();
    }

    public function testReleaseWhenStockIsZero()
    {
        $product = Product::factory()->create(['stock' => 0, 'max_quantity' => 5]);
        $orderItem = OrderItem::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'reserved' => true,
        ]);

        $orderItem->releaseProduct();

        $orderItem->refresh();
        $product->refresh();

        $this->assertFalse($orderItem->reserved);
        $this->assertSame($product->stock, 0);
    }
}
