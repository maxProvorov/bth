<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProductCreated;

class SendProductNotification implements ShouldQueue
{
    use Queueable;

    protected $product;

    /**
     * Create a new job instance.
     */

    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = config('products.email');
        if ($email) {
            Notification::route('mail', $email)->notify(new ProductCreated($this->product));
        }
    }
}
