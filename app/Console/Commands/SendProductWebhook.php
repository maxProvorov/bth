<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class SendProductWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:send-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the product with the highest ID to the webhook URL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookUrl = config('products.webhook');

        if (!$webhookUrl) {
            $this->error('Webhook URL is not configured.');
            return;
        }

        $product = Product::orderBy('id', 'desc')->first();

        if (!$product) {
            $this->info('No products found.');
            return;
        }

        $response = Http::post($webhookUrl, [
            'id' => $product->id,
            'name' => $product->name,
            'article' => $product->article,
            'status' => $product->status,
            'data' => $product->data,
        ]);
    }
}
