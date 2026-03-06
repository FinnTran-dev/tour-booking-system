<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * List all invoices with optional status filter.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');

        $invoices = Invoice::with(['booking.tour', 'booking.tourDate'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => InvoiceResource::collection($invoices),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'total'        => $invoices->total(),
            ],
        ]);
    }

    /**
     * Mark an invoice as Paid or Cancelled.
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:Unpaid,Paid,Cancelled'],
        ]);

        $invoice->update(['status' => $request->status]);

        return response()->json([
            'data' => new InvoiceResource($invoice->load('booking'))
        ]);
    }
}
