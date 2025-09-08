<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRazorpayPaymentGateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $razorpay = DB::table('payment_gateways')->where('name', '=', 'Razorpay_Checkout')->first();

        if ($razorpay === null) {
            DB::table('payment_gateways')->insert([
                'provider_name' => 'Razorpay',
                'provider_url' => 'https://razorpay.com',
                'is_on_site' => 0,
                'can_refund' => 1,
                'name' => 'Razorpay_Checkout',
                'default' => 0,
                'admin_blade_template' => 'ManageAccount.Partials.Razorpay',
                'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentRazorpay'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $razorpay = DB::table('payment_gateways')->where('name', '=', 'Razorpay_Checkout')->first();

        if ($razorpay) {
            // Set any orders using this gateway to null to avoid foreign key constraints
            DB::table('orders')->where('payment_gateway_id', $razorpay->id)->update(['payment_gateway_id' => null]);
            
            // Delete the gateway
            DB::table('payment_gateways')->where('name', '=', 'Razorpay_Checkout')->delete();
        }
    }
}
