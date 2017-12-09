<?php

namespace App\Http\Controllers;
use App\Order;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Excel;

class AdminOrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();
        return view('auth.order.index')->with('orders', $orders);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $order = Order::find($id);
        return view('auth.order.edit')->with('order', $order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $shipping = $request->Input('shipping_status');
        if ($shipping == 1)
        {
            $order->update(['address' => $request->Input('address'), 'shipping_status' => 0, 'phone' => $request->Input('phone'), 'name' => $request->Input('name'), 'status' => 1 ]);
        }
        elseif ( $shipping == 2)
        {
            $order->update(['address' => $request->Input('address'), 'shipping_status' => 1, 'phone' => $request->Input('phone'), 'name' => $request->Input('name'), 'status' => 0  ]);
        }
        else
        {
            $order->update(['address' => $request->Input('address'), 'shipping_status' => 2, 'phone' => $request->Input('phone'), 'name' => $request->Input('name'), 'status' => 0  ]);
        }

        return redirect('adminpc/orders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {
        $date_start = $request->Input('date_start');
        $date_end = $request->Input('date_end');
        $status = $request->Input('status');
        if (empty($date_start) && empty($date_end) )
        {
            if($request->Input('status') == 2)
            {
                $orders = Order::all();
            }
            elseif ($request->Input('status') == 1) {
                $orders = Order::where('status', '=', 1)->get();
            }
            else
            {
                $orders = Order::where('status', '=', 0)->get();
            }
            return view('auth.order.index')->with(['orders' => $orders, 'date_start' => $date_start, 'date_end' => $date_end, 'status' => $status]);
        }
        elseif ($request->Input('status') == 2) {
            $orders = Order::where('created_at', '>=', $date_start)->where('created_at', '<=', $date_end)->get();
        }
        elseif ($request->Input('status') == 1) {
            $orders = Order::where('created_at', '>=', $date_start)->where('created_at', '<=', $date_end)->where('status', '=', 1)->get();
        }
        else
        {
            $orders = Order::where('created_at', '>=', $date_start)->where('created_at', '<=', $date_end)->where('status', '=', 0)->get();
        }

        return view('auth.order.index')->with(['orders' => $orders, 'date_start' => $date_start, 'date_end' => $date_end, 'status' => $status]);
       /* dd($status);
        // dd(strtotime($date_end));
        $orders = Order::where('date', '>=', $date_start)->where('date', '<=', $date_end)->get();
        return view('auth.order.index')->with('orders', $orders);*/
    }

    public function export_order(Request $request)
    {
        $date_start = $request->Input('date_start');
        $date_end = $request->Input('date_end');
        $status = $request->Input('status');
        if (empty($date_start) && empty($date_end) )
        {
            if($request->Input('status') == 2)
            {
                $orders = Order::all();
            }
            elseif ($request->Input('status') == 1) {
                $orders = Order::where('status', '=', 'avalible')->get();
            }
            else
            {
                $orders = Order::where('status', '=', 'not avalible')->get();
            }
            Excel::create('Orders Excel', function($excel) use($orders) {
                $excel->sheet('Excel sheet', function($sheet) use($orders) {
                    $sheet->fromArray($orders);
                });
            })->export('xls');
            return redirect('admin/orders');
        }
        elseif ($request->Input('status') == 2) {
            $orders = Order::where('date', '>=', $date_start)->where('date', '<=', $date_end)->get();
        }
        elseif ($request->Input('status') == 1) {
            $orders = Order::where('date', '>=', $date_start)->where('date', '<=', $date_end)->where('status', '=', 'avalible')->get();
        }
        else
        {
            $orders = Order::where('date', '>=', $date_start)->where('date', '<=', $date_end)->where('status', '=', 'not avalible')->get();
        }
        Excel::create('Orders Excel', function($excel) use($orders) {
            $excel->sheet('Excel sheet', function($sheet) use($orders) {
                $sheet->fromArray($orders);
            });
        })->export('xls');
        return redirect('admin/orders');
    }
        public function report()
    {
        $orders = Order::all();
        return view('auth.order.report')->with('orders', $orders);

    }
    public function report_search (Request $request)
    {

        $date_start = $request->Input('date_start');
        $date_end = $request->Input('date_end');
        if (empty($date_start) && empty($date_end) )
        {
            $orders = $orders = Order::where('date', '>=', '0/0/0')->where('date', '<=', '0/0/0')->get();
            $total = total_summary(12);
            return view('auth.order.report')->with(['orders' => $orders, 'total' => $total]);
        }
        elseif(!empty($date_start) && empty($date_end))
        {
            $orders = Order::where('date', '>=', $date_start)->get();
            return view('auth.order.report')->with(['orders' => $orders]);
        }
        elseif (empty($date_start) && !empty($date_end)) {
            $orders = Order::where('date', '<=', $date_end )->get();
            return view('auth.order.report')->with(['orders' => $orders]);
        }
        $orders = Order::where('date', '>=', $date_start)->where('date', '<=', $date_end)->get();
        return view('auth.order.report')->with(['orders' => $orders]);
    }

    public function total_summary($id)
    {
        return $id;
    }
}