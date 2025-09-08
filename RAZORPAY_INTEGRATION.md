# Razorpay Integration for Attendize

This document explains how to set up Razorpay payment gateway integration in your Attendize installation.

## Overview

Razorpay has been successfully integrated into Attendize with the following features:
- Secure payment processing through Razorpay Checkout
- Support for multiple currencies (as supported by Razorpay)
- Refund capabilities
- Test and live mode support
- Admin configuration panel
- Responsive checkout interface

## Installation Steps

### 1. Install Required Package

First, you need to install the Razorpay Omnipay package:

```bash
composer require razorpay/omnipay-razorpay:~2.0
```

### 2. Run Database Migrations

Run the migration to add Razorpay to your payment gateways:

```bash
php artisan migrate
```

Alternatively, you can run the seeder:

```bash
php artisan db:seed --class=PaymentGatewaySeeder
```

### 3. Configure Razorpay Account

1. Sign up at [Razorpay Dashboard](https://dashboard.razorpay.com/)
2. Get your API keys:
   - **Key ID**: Your publishable key (starts with `rzp_test_` for test mode or `rzp_live_` for live mode)
   - **Key Secret**: Your secret key
3. Configure webhook URLs in your Razorpay dashboard (optional but recommended)

### 4. Configure in Attendize

1. Log into your Attendize admin panel
2. Go to **Account Settings** → **Payment Gateways**
3. Select **Razorpay** from the available gateways
4. Enter your Razorpay credentials:
   - **Razorpay Key ID**: Your API Key ID
   - **Razorpay Key Secret**: Your API Key Secret
   - **Test Mode**: Check this for testing, uncheck for live payments
5. Save the configuration

### 5. Set as Default (Optional)

To make Razorpay your default payment gateway:
1. In the payment gateway settings, set Razorpay as default
2. Or update the `default_payment_gateway` setting in `config/attendize.php` to `3`

## Files Added/Modified

### New Files Created:
- `app/Services/PaymentGateway/Razorpay.php` - Razorpay service class
- `resources/views/ManageAccount/Partials/Razorpay.blade.php` - Admin config template
- `resources/views/Public/ViewEvent/Partials/PaymentRazorpay.blade.php` - Checkout template
- `database/migrations/2024_09_07_120000_add_razorpay_payment_gateway.php` - Database migration

### Modified Files:
- `composer.json` - Added Razorpay package dependency
- `app/Services/PaymentGateway/Factory.php` - Added Razorpay support
- `config/attendize.php` - Added Razorpay gateway constant
- `database/seeds/PaymentGatewaySeeder.php` - Added Razorpay seeding
- `database/factories/PaymentGateway.php` - Added Razorpay factory state
- `resources/lang/en/ManageAccount.php` - Added Razorpay language strings
- `resources/lang/en/Public_ViewEvent.php` - Added Razorpay language strings

## Configuration Values

The following configuration constants have been added:

```php
'payment_gateway_razorpay' => 3,
'default_payment_gateway' => 1, // Change to 3 for Razorpay as default
```

## Testing

### Test Credentials
For testing, use these Razorpay test credentials:
- **Key ID**: Get from your Razorpay test dashboard
- **Key Secret**: Get from your Razorpay test dashboard
- **Test Mode**: Enabled

### Test Cards
Use these test card numbers in test mode:
- **Success**: 4111 1111 1111 1111
- **Failure**: 4000 0000 0000 0002
- **CVV**: Any 3 digits
- **Expiry**: Any future date

## Features Supported

✅ **Payment Processing**: Full payment processing through Razorpay Checkout
✅ **Refunds**: Complete and partial refunds
✅ **Multiple Currencies**: All currencies supported by Razorpay
✅ **Test Mode**: Separate test and live environments
✅ **Mobile Responsive**: Optimized for mobile devices
✅ **Security**: PCI DSS compliant through Razorpay
✅ **Webhooks**: Ready for webhook integration
✅ **Order Management**: Complete order tracking

## Security Considerations

- All sensitive data is handled by Razorpay's secure servers
- Payment details never touch your server
- Signature verification ensures payment authenticity
- Test/Live mode separation prevents accidental live charges during testing

## Support

For Razorpay-related issues:
- [Razorpay Documentation](https://razorpay.com/docs/)
- [Razorpay Support](https://razorpay.com/support/)

For Attendize integration issues:
- Check the logs in `storage/logs/`
- Ensure all dependencies are installed
- Verify configuration settings

## Troubleshooting

### Common Issues:

1. **"Razorpay not available"**
   - Ensure the migration has been run
   - Check that the gateway is enabled in admin panel

2. **"Invalid API key"**
   - Verify your Key ID and Key Secret
   - Ensure you're using the correct test/live keys

3. **Payment not completing**
   - Check browser console for JavaScript errors
   - Verify webhook configuration
   - Check Razorpay dashboard for payment status

### Debug Mode:
Enable Laravel debug mode in your `.env` file:
```
APP_DEBUG=true
```

This will show detailed error messages to help troubleshoot issues.
