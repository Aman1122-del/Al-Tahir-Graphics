<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CartItem;

class FixCartItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:fix-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing cart items by adding default variant values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking existing cart items...');

        $cartItems = CartItem::all();
        $this->info("Found {$cartItems->count()} cart items");

        $updatedCount = 0;

        foreach ($cartItems as $item) {
            $updated = false;
            
            if (is_null($item->size)) {
                $item->size = 'standard';
                $updated = true;
            }
            
            if (is_null($item->paper_type)) {
                $item->paper_type = 'standard';
                $updated = true;
            }
            
            if (is_null($item->finish)) {
                $item->finish = 'matte';
                $updated = true;
            }
            
            if ($updated) {
                $item->save();
                $updatedCount++;
                $this->line("Updated cart item ID: {$item->id}");
            }
        }

        $this->info("Fixed {$updatedCount} cart items");
        $this->info('Cart items fix completed!');
    }
}
