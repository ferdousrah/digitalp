<?php
namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email:rfc|max:255']);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            ['is_active' => true, 'subscribed_at' => now()]
        );

        // Re-subscribe if they had previously opted out
        if (!$subscriber->wasRecentlyCreated && !$subscriber->is_active) {
            $subscriber->update([
                'is_active'       => true,
                'subscribed_at'   => now(),
                'unsubscribed_at' => null,
            ]);
        }

        $message = $subscriber->wasRecentlyCreated
            ? 'Thanks for subscribing — keep an eye on your inbox.'
            : "You're already on the list — thanks!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'new'     => $subscriber->wasRecentlyCreated,
            ]);
        }

        return back()->with('success', $message);
    }
}
