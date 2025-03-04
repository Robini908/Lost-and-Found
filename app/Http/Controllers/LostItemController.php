<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use Illuminate\Http\Request;

class LostItemController extends Controller
{
    /**
     * Display the specified lost item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = LostItem::findOrFail($id);

        if (!app('role-permission')->canViewItemDetails(auth()->user(), $item)) {
            abort(403, 'Unauthorized action.');
        }

        return view('lost-items.show', compact('item'));
    }

    public function details($id)
    {
        $item = LostItem::with(['images', 'user'])->findOrFail($id);
        return view('lost-items.details', compact('item'));
    }

    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);

        if (!app('role-permission')->canDeleteItem(auth()->user(), $item)) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();
        return redirect()->route('products.view-items')->with('success', 'Item deleted successfully');
    }
}
