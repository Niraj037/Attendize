<section class="payment_gateway_options" id="gateway_{{$payment_gateway['id']}}">
    <h4>@lang("ManageAccount.razorpay_settings")</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('razorpay[keyId]', trans("ManageAccount.razorpay_key_id"), array('class'=>'control-label ')) !!}
                {!! Form::text('razorpay[keyId]', $account->getGatewayConfigVal($payment_gateway['id'], 'keyId'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('razorpay[keySecret]', trans("ManageAccount.razorpay_key_secret"), array('class'=>'control-label ')) !!}
                {!! Form::password('razorpay[keySecret]', [ 'class'=>'form-control', 'value' => $account->getGatewayConfigVal($payment_gateway['id'], 'keySecret')])  !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">@lang("ManageAccount.test_mode")</label>
                <div class="checkbox">
                    {!! Form::checkbox('razorpay[testMode]', 1, $account->getGatewayConfigVal($payment_gateway['id'], 'testMode'), ['id' => 'razorpay_test_mode']) !!}
                    <label for="razorpay_test_mode">@lang("ManageAccount.enable_test_mode")</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="ico-info"></i>
                @lang("ManageAccount.razorpay_help_text")
            </div>
        </div>
    </div>
</section>
