<form class="online_payment" action="<?php echo route('postCreateOrder', ['event_id' => $event->id]); ?>" method="post" id="razorpay-payment-form">
    <div class="form-row">
        <div class="alert alert-info">
            <i class="ico-info"></i>
            @lang("Public_ViewEvent.razorpay_payment_info")
        </div>
    </div>
    {!! Form::token() !!}

    <input class="btn btn-lg btn-success card-submit" style="width:100%;" type="submit" value="@lang("Public_ViewEvent.pay_with_razorpay")">

</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">

document.addEventListener('DOMContentLoaded', function() {
    var razorpayForm = document.getElementById('razorpay-payment-form');
    
    if (razorpayForm) {
        razorpayForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var options = {
                "key": "<?php echo $account_payment_gateway->config['keyId']; ?>",
                "amount": <?php echo $orderService->getOrderTotalWithBookingFee() * 100; ?>, // Amount in paise
                "currency": "<?php echo $event->currency->code; ?>",
                "name": "<?php echo $event->title; ?>",
                "description": "Ticket for <?php echo $event->title; ?>",
                "image": "<?php echo $event->organiser->full_logo_path; ?>",
                "handler": function (response){
                    // Add payment details to form
                    var form = document.getElementById('razorpay-payment-form');
                    
                    var paymentIdInput = document.createElement('input');
                    paymentIdInput.setAttribute('type', 'hidden');
                    paymentIdInput.setAttribute('name', 'razorpay_payment_id');
                    paymentIdInput.setAttribute('value', response.razorpay_payment_id);
                    form.appendChild(paymentIdInput);
                    
                    var orderIdInput = document.createElement('input');
                    orderIdInput.setAttribute('type', 'hidden');
                    orderIdInput.setAttribute('name', 'razorpay_order_id');
                    orderIdInput.setAttribute('value', response.razorpay_order_id);
                    form.appendChild(orderIdInput);
                    
                    var signatureInput = document.createElement('input');
                    signatureInput.setAttribute('type', 'hidden');
                    signatureInput.setAttribute('name', 'razorpay_signature');
                    signatureInput.setAttribute('value', response.razorpay_signature);
                    form.appendChild(signatureInput);
                    
                    // Submit form using AJAX
                    var ajaxFormConf = getAjaxFormConfig($(form));
                    $(form).ajaxSubmit(ajaxFormConf);
                },
                "prefill": {
                    "name": "<?php echo isset($attendees[0]['first_name']) ? $attendees[0]['first_name'] . ' ' . $attendees[0]['last_name'] : ''; ?>",
                    "email": "<?php echo isset($attendees[0]['email']) ? $attendees[0]['email'] : ''; ?>"
                },
                "notes": {
                    "event_id": "<?php echo $event->id; ?>",
                    "event_title": "<?php echo $event->title; ?>"
                },
                "theme": {
                    "color": "<?php echo $event->bg_color; ?>"
                },
                "modal": {
                    "ondismiss": function(){
                        // Handle payment cancellation
                        console.log('Payment cancelled by user');
                    }
                }
            };
            
            var rzp = new Razorpay(options);
            rzp.open();
        });
    }
});

</script>
