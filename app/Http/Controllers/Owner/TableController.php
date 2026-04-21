<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    private function restaurant() { return Auth::user()->ownedRestaurant; }

    public function index()
    {
        $restaurant = $this->restaurant();
        $rooms = $restaurant->rooms()->with('tables')->get();
        return view('owner.tables.index', compact('restaurant', 'rooms'));
    }

    // --- Rooms ---
    public function storeRoom(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string']);
        $this->restaurant()->rooms()->create($data);
        return back()->with('success', 'Room added.');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string']);
        $room->update($data);
        return back()->with('success', 'Room updated.');
    }

    public function destroyRoom(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Room deleted.');
    }

    // --- Tables ---
    public function storeTable(Request $request)
    {
        $data = $request->validate([
            'room_id'  => 'required|exists:rooms,id',
            'number'   => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
        ]);
        $data['restaurant_id'] = $this->restaurant()->id;
        Table::create($data);
        return back()->with('success', 'Table added.');
    }

    public function destroyTable(Table $table)
    {
        $table->delete();
        return back()->with('success', 'Table deleted.');
    }

    public function showQr(Table $table)
    {
        $url = route('customer.scan', $table->qr_token);
        $qrCode = QrCode::format('svg')->size(300)->generate($url);
        return view('owner.tables.qr', compact('table', 'qrCode', 'url'));
    }

    public function downloadQr(Table $table)
    {
        $url = route('customer.scan', $table->qr_token);
        $qrCode = QrCode::format('png')->size(400)->generate($url);
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"table-{$table->number}-qr.png\"");
    }
}
