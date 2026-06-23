<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ReviewRequestService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched (after-response, so it never blocks the admin) when an order is delivered.
 * Runs inline when there's no queue worker, or on the queue when one is configured.
 */
class SendReviewRequest
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function handle(ReviewRequestService $service): void
    {
        $service->sendForOrder($this->order);
    }
}
