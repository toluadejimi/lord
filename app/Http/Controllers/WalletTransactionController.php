<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletTransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->query('filter', 'all');

        $query = Transaction::where('user_id', $userId)->latest();

        if ($filter === 'credit') {
            $query->whereIn('type', [2, 3]);
        } elseif ($filter === 'debit') {
            $query->whereIn('type', [1, 4]);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $stats = [
            'wallet' => (float) Auth::user()->wallet,
            'total_credit' => (float) Transaction::where('user_id', $userId)
                ->where('status', 2)
                ->whereIn('type', [2, 3])
                ->sum('amount'),
            'total_debit' => (float) Transaction::where('user_id', $userId)
                ->where('status', 2)
                ->whereIn('type', [1, 4])
                ->sum('amount'),
        ];

        return view('wallet.transactions', [
            'transactions' => $transactions,
            'filter' => $filter,
            'stats' => $stats,
        ]);
    }
}
