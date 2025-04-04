<?php

namespace App\Http\Controllers;

use App\Imports\MainItemsClass;
use App\Models\Category;
use App\Models\Item;
use App\Models\MainItem;
use App\Models\Product;
use App\Models\SoldLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function buy_now(request $request)
    {


        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {

            $message = Auth::user()->email . " has been banned for cheating";
            send_notification($message);
            send_notification2($message);

            User::where('id', Auth::id())->update(['status' => 9]);
            Auth::logout();
            return redirect('ban');

        }


        $amount = Item::where('id', $request->item_id)->first()->amount;
        $product_id = Item::where('id', $request->item_id)->first()->product_id;


        $pamount = $amount * $request->quantity;
        $in_stock = MainItem::where('product_id', $product_id)->count();


        if ($pamount > Auth::user()->wallet) {

            return back()->with('error', 'Insufficient Funds, Fund your wallet');

        }


        if ($in_stock < $request->quantity) {

            return back()->with('error', "Insufficient Stock, You can only purchase $in_stock / pcs at the moment");

        }


        User::where('id', Auth::id())->decrement('wallet', $pamount);

        $get_item = MainItem::select('name')->where('product_id', $product_id)->take($request->quantity)->get();


        $formattedRow = [];
        foreach ($get_item as $value) {
            $formattedRow[] = $value['name'];
        }
        $text = implode("\n", $formattedRow) . "\n";
        $filename = date('ymdhis') . 'data.txt';
        Storage::disk('local')->put($filename, $text);

        $get_item = MainItem::whereIn('name', $get_item->pluck('name'))
            ->delete();

        Item::where('product_id', $product_id)->decrement('qty', $request->quantity);

        $url = url('') . "/storage/app/$filename";


        $ref = random_int(000, 999) . date('ymdhis');

        $trx = new Transaction();
        $trx->user_id = Auth::id();
        $trx->amount = $pamount;
        $trx->type = 1;
        $trx->ref_id = $ref;
        $trx->status = 1;
        $trx->save();

        $sl = new SoldLog();
        $sl->user_id = Auth::id();
        $sl->ref_id = $ref;
        $sl->amount = $pamount;
        $sl->status = 1;
        $sl->qty = $request->quantity;
        $sl->item = $url;
        $sl->save();


        $data['main_url'] = "<a href='$url'> CLICK HERE TO VIEW YOUR ORDER 👉🏽 DOWNLOAD </a>";
        $data['url'] = url('') . "/storage/app/$filename";
        $data['user'] = Auth::id() ?? null;
        $data['fbaged'] = Category::where('id', 1)->get();
        $data['insta_cat'] = Category::where('id', 2)->get();
        $data['ot'] = Category::where('id', 3)->get();
        $data['tw'] = Category::where('id', 5)->get();
        $data['rd'] = Category::where('id', 6)->get();
        $data['ml'] = Category::where('id', 7)->get();
        $data['gv'] = Category::where('id', 8)->get();
        $data['in'] = Category::where('id', 9)->get();
        $data['tk'] = Category::where('id', 12)->get();
        $data['ln'] = Category::where('id', 13)->get();
        $data['pv'] = Category::where('id', 14)->get();
        $data['oth'] = Category::where('id', 15)->get();
        $data['swr'] = Category::where('id', 16)->get();
        $data['snap'] = Category::where('id', 17)->get();
        $data['strem'] = Category::where('id', 18)->get();
        $data['resell'] = Category::where('id', 19)->get();
        $data['special'] = Category::where('id', 20)->get();


        $data['fbaged_items'] = Item::where('cat_id', 1)->take(5)->get();
        $data['insta_items'] = Item::where('cat_id', 2)->take(5)->get();
        $data['ot_items'] = Item::where('cat_id', 3)->take(5)->get();
        $data['tw_items'] = Item::where('cat_id', 5)->take(5)->get();
        $data['rd_items'] = Item::where('cat_id', 6)->take(5)->get();
        $data['ml_items'] = Item::where('cat_id', 7)->take(5)->get();
        $data['gv_items'] = Item::where('cat_id', 8)->take(5)->get();
        $data['in_items'] = Item::where('cat_id', 9)->take(5)->get();
        $data['tk_items'] = Item::where('cat_id', 12)->take(5)->get();
        $data['ln_items'] = Item::where('cat_id', 13)->take(5)->get();
        $data['pv_items'] = Item::where('cat_id', 14)->take(5)->get();
        $data['oth_items'] = Item::where('cat_id', 15)->take(5)->get();
        $data['swr_items'] = Item::where('cat_id', 16)->take(5)->get();
        $data['snap_items'] = Item::where('cat_id', 17)->take(5)->get();
        $data['strem_items'] = Item::where('cat_id', 18)->take(5)->get();
        $data['resell_items'] = Item::where('cat_id', 19)->take(5)->get();
        $data['special_items'] = Item::where('cat_id', 20)->take(5)->get();


        return view('welcome', $data);


    }


    public function item_view(request $request)
    {


        $user = Auth::id();
        if ($user == null) {
            return back()->with('error', 'Login your account to buy product');
        }


        $item_id = $request->id;

        $product_id = Item::where('id', $request->id)->first()->product_id;
        $title = Item::where('id', $request->id)->first()->title;
        $amount = Item::where('id', $request->id)->first()->amount;
        $stock = MainItem::where('product_id', $product_id)->count();


        $inst = Item::where('id', $request->id)->first()->inst;
        $description = Item::where('id', $request->id)->first()->description;

        $instruction = Item::where('id', $request->id)->first()->instruction;
        $description = Item::where('id', $request->id)->first()->description;


        $item_id = $request->id;
        $user = Auth::id() ?? null;


        return view('item-view', compact('title', 'inst', 'description', 'item_id', 'stock', 'amount', 'user'));


    }

    public function i_view(request $request)
    {


        $user = Auth::id();
        if ($user == null) {
            return back()->with('error', 'Login your account to buy product');
        }
        $item_id = $request->id;

        $product_id = Item::where('id', $request->id)->first()->product_id;
        $title = Item::where('id', $request->id)->first()->title;
        $amount = Item::where('id', $request->id)->first()->amount;
        $stock = MainItem::where('product_id', $product_id)->count();


        $instruction = Item::where('id', $request->id)->first()->instruction;
        $description = Item::where('id', $request->id)->first()->description;


        return view('item-view', compact('title', 'instruction', 'description', 'item_id', 'stock', 'amount', 'user'));


    }

    public function view_all_product(request $request)
    {


        $title = Category::where('id', $request->cat_id)->first()->title;

        $items = Item::where('cat_id', $request->cat_id)->get();

        $user = Auth::id() ?? null;


        return view('view-all', compact('title', 'user', 'items'));


    }


    public function add_new_product(Request $request)
    {


        Item::where('product_id', $request->pr_id)->increment('qty', $request->qty);


        $file = $request->file('file');

        Excel::import(new MainItemsClass, $file);


        return back()->with('message', "Stock has been successfully added ");


    }


    public function add_front_product(Request $request)
    {


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/content/images'), $imageName); // Save the image to the "uploads" directory in the public folder
        }


        $title = Product::where('id', $request->pro_id)->first()->name;

        $tr = new Item();
        $tr->title = $title;
        $tr->amount = $request->amount;
        $tr->product_id = $request->pro_id;
        $tr->cat_id = $request->cat_id;
        $tr->icon = $imageName;
        $tr->save();


        return back()->with('message', "Front Product Created successfully ");


    }


    public function delete_front_product(Request $request)
    {
        Item::where('id', $request->id())->delete();
        return back()->with('message', "Front Product Deleted successfully ");


    }

    public function e_fund(request $request)
    {


        $ipb = env('IPA');
        $ipa = env('IPB');
        $ip = $request->ip();



     if($ip == $ipb || $ip == $ipa) {

         $get_user = User::where('email', $request->email)->first() ?? null;

         if ($get_user == null) {

             return response()->json([
                 'status' => false,
                 'message' => 'No one user found, please check email and try again',
             ]);
         }

         $ip = $request->ip();
         $url = $request->url();
         $message = "SMSLORD - just funded his wallet | $request->email | $request->amount | $ip | $url | on SMSLORD";
         send_notification($message);
         send_notification2($message);



         User::where('email', $request->email)->increment('wallet', $request->amount) ?? null;

         $amount = number_format($request->amount, 2);


         $get_depo = Transaction::where('ref_id', $request->order_id)->first() ?? null;
         if ($get_depo == null){
             $trx = new Transaction();
             $trx->ref_id = $request->order_id;
             $trx->user_id = $get_user->id;
             $trx->status = 2;
             $trx->amount = $request->amount;
             $trx->type = 2;
             $trx->save();
         }else{
             Transaction::where('ref_id', $request->order_id)->update(['status'=> 2]);
         }



         return response()->json([
             'status' => true,
             'message' => "NGN $amount has been successfully added to your wallet",
         ]);

     }else{
         $ip = $request->ip();
         $url = $request->url();
         $message = "SMSLORD - just trying to fund | $request->email | $request->amount | $ip | $url | on SMSLORD";
         send_notification($message);
         send_notification2($message);


         return response()->json([
             'status' => true,
             'message' => "Something went wrong",
         ]);

     }



    }


    public function verify_username(request $request)
    {

        $get_user =  User::where('email', $request->email)->first() ?? null;

        if($get_user == null){

            return response()->json([
                'username' => "Not Found, Pleas try again"
            ]);

        }

        return response()->json([
            'username' => $get_user->username
        ]);



    }



}
