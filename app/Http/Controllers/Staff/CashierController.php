<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\TableSession;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CashierController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    public function index()
    {
        $restaurant = Auth::user()->restaurant;
        $activeSessions = $restaurant->sessions()
            ->with('table.room', 'orders', 'bill')
            ->where('status', 'active')
            ->get();
        return view('staff.cashier', compact('restaurant', 'activeSessions'));
    }

    public function session(TableSession $session)
    {
        abort_if($session->restaurant_id !== Auth::user()->restaurant_id, 403);
        $session->load('table.room', 'orders.items.menuItem', 'bill');
        $bill = $this->billingService->generateOrUpdate($session);
        return view('staff.cashier-session', compact('session', 'bill'));
    }

    public function markPaid(Request $request, TableSession $session)
    {
        abort_if($session->restaurant_id !== Auth::user()->restaurant_id, 403);
        $request->validate(['payment_method' => 'required|in:cash,card,online']);

        $bill = $this->billingService->generateOrUpdate($session);
        $this->billingService->markPaid($bill, $request->payment_method);

        // Close session and free the table
        $session->update(['status' => 'closed', 'closed_at' => now()]);
        $session->table->update(['status' => 'free']);

        // Mark all orders completed
        $session->orders()->whereNotIn('status', ['rejected','cancelled'])->update(['status' => 'completed']);

        return redirect()->route('cashier.index')->with('success', 'Payment confirmed. Table freed.');
    }

    public function printBill(TableSession $session)
    {
        abort_if($session->restaurant_id !== Auth::user()->restaurant_id, 403);
        $session->load('table.room', 'orders.items.menuItem', 'bill', 'restaurant');
        $bill = $this->billingService->generateOrUpdate($session);
        $pdf = Pdf::loadView('pdf.bill', compact('session', 'bill'));
        return $pdf->stream("bill-{$session->id}.pdf");
    }
}
