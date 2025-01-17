<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Inisialisasi variabel untuk data penjualan

        $salesData = [
            'confirmed' => [],
        ];

        $salesData['confirmed'] = DB::table('order')
            ->selectRaw('MONTH(order.created_at) as month, brands.name as umkm_name, SUM(order_detail.qty) as total_quantity_sold')
            ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->join('products', 'products.id', '=', 'order_detail.product_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('users', 'users.id', '=', 'brands.users_id')
            ->whereIn('order.status', ['SUDAH DIKONFIRMASI', 'Sudah Dikonfirmasi'])
            ->groupBy('month', 'brands.name')
            ->get()
            ->groupBy('month')
            ->toArray();

        if (Auth::user()->hasRole('super admin')) {
            // Logika untuk super admin
            $product = DB::table('products')
                ->select(DB::raw('count(*) as total_produk'))
                ->first();

            $order = DB::table('order')
                ->select(DB::raw('count(*) as total_order'))
                ->first();

            $pending_transaksi = DB::table('order')
                ->select(DB::raw('count(*) as total_orders'))
                ->where('order.status', '=', 'PENDING')
                ->orWhere('order.status', '=', 'pending')
                ->first();

            $transaksi_sukses = DB::table('order')
                ->select(DB::raw('count(*) as total_orders'))
                ->whereIn('order.status', ['PENDING', 'pending'])
                ->first();

        } elseif (Auth::user()->hasRole('brand')) {
            // Logika untuk brand
            $product = DB::table('products')
                ->select(DB::raw('count(*) as total_produk'))
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->join('users', 'users.id', '=', 'brands.users_id')
                ->where('users.id', Auth::user()->id)
                ->first();

            $order = DB::table('order')
                ->select(DB::raw('count(*) as total_order'))
                ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
                ->join('products', 'products.id', '=', 'order_detail.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->join('users', 'users.id', '=', 'brands.users_id')
                ->where('users.id', Auth::user()->id)
                ->groupBy('brands.id', 'brands.name')
                ->first();

            $pending_transaksi = DB::table('order')
                ->select(DB::raw('count(*) as total_orders'))
                ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
                ->join('products', 'products.id', '=', 'order_detail.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->join('users', 'users.id', '=', 'brands.users_id')
                ->where('order.status', '=', 'PENDING')
                ->orWhere('order.status', '=', 'pending')
                ->where('users.id', Auth::user()->id)
                ->groupBy('brands.id', 'brands.name')
                ->first();

            $transaksi_sukses = DB::table('order')
                ->select(DB::raw('count(*) as total_orders'))
                ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
                ->join('products', 'products.id', '=', 'order_detail.product_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->join('users', 'users.id', '=', 'brands.users_id')
                ->whereIn('order.status', ['PENDING', 'pending'])
                ->where('users.id', Auth::user()->id)
                ->groupBy('brands.id', 'brands.name')
                ->first();

            
        }


        return view('pages.dashboard', [
            'order' => $order,
            'product' => $product,
            'pending_transaksi' => $pending_transaksi,
            'transaksi_sukses' => $transaksi_sukses,
            'salesData' => $salesData,  // Kirim data penjualan ke view
        ]);
    }


    public function dashboard_customer()
    {
        // Logika untuk dashboard customer bisa ditambahkan di sini
    }
}
