<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        // Let Laravel handle the default login via existing routes/controllers if present.
        // Here we only perform cart merge post-authentication.
        // This controller can be wired if not already using Breeze/Fortify; otherwise, hook merge logic after login event.
    }

    public static function mergeGuestCartToUser(): void
    {
        $sessionId = Session::getId();
        $userId = Auth::id();
        if (!$userId) { return; }

        $guestItems = CartItem::where('session_id', $sessionId)->get();
        foreach ($guestItems as $item) {
            $existing = CartItem::where('user_id', $userId)
                ->where('service_id', $item->service_id)
                ->where('service_sample_id', $item->service_sample_id)
                ->where('unit_price', $item->unit_price)
                ->where('custom_requirements', $item->custom_requirements)
                ->first();
            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $item->quantity]);
                $item->delete();
            } else {
                $item->update(['user_id' => $userId, 'session_id' => null]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
