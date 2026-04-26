<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orderCount = $this->ordersForUser($user)->count();
        $recent = $this->ordersForUser($user)->latest()->limit(5)->get();

        return view('account.index', [
            'user'       => $user,
            'orderCount' => $orderCount,
            'recent'     => $recent,
        ]);
    }

    public function orders()
    {
        $orders = $this->ordersForUser(Auth::user())
            ->latest()
            ->paginate(15);

        return view('account.orders', compact('orders'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:120',
            'email' => 'nullable|email|max:255',
        ]);

        $user = Auth::user();
        // If email is being set, ensure it's unique among other users
        if (!empty($data['email'])) {
            $exists = \App\Models\User::where('email', $data['email'])
                ->where('id', '!=', $user->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['email' => 'This email is already in use.']);
            }
        }

        $user->fill($data)->save();

        return back()->with('success', 'Profile updated.');
    }

    /**
     * Match orders to the signed-in user.
     * Orders historically had no user_id (guest checkout). Match by phone too —
     * normalized phone equals the order's billing/shipping phone.
     */
    protected function ordersForUser($user)
    {
        $query = Order::query();

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if ($user->phone) {
                // Strip + and country code variations to match historical orders that store local form
                $local = ltrim(preg_replace('/^\+?880/', '', $user->phone), '0');
                $candidates = array_unique([
                    $user->phone,
                    '0' . $local,
                    '880' . $local,
                    '+880' . $local,
                ]);
                $q->orWhereIn('shipping_phone', $candidates)
                  ->orWhereIn('billing_phone', $candidates);
            }
        });
    }
}
