{{-- <div class="overflow-hidden">
    <ul class="nav nav-tabs mb-5 pb-1 overflow-auto flex-nowrap text-nowrap" id="myTab" role="tablist">
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#stripeForm">
                {{__('messages.bill.stripe')}}
            </a>
        </li>
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#PayPalForm">{{__('messages.paypal')}}</a>
        </li>
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#RazorPayForm">{{__('messages.razorpay')}}</a>
        </li>
    </ul>
</div>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="stripeForm" role="tabpanel">
        @include('payment_gateway.stripe')
    </div>
    <div class="tab-pane fade" id="PayPalForm" role="tabpanel">
        @include('payment_gateway.paypal')
    </div>
    <div class="tab-pane fade" id="RazorPayForm" role="tabpanel">
        @include('payment_gateway.razorpay')
    </div>
</div> --}}

<div class="card">
    <div class="card-body">
        {{ Form::open(['route' => 'payment-gateways.store', 'id' => 'UserCredentialsSettings', 'class' => 'form']) }}
        <div class="row">
            {{-- STRIPE --}}
            <div class="col-12 d-flex align-items-center">
                <span class="form-label my-3">{{ __('messages.bill.stripe') . ' :' }}</span>
                <label class="form-check form-switch form-switch-sm ms-3">
                    <input type="checkbox" name="stripe_enable" class="form-check-input stripe-enable" value="1"
                        {{ !empty($credentials['stripe_enable']) == '1' ? 'checked' : '' }} id="stripeEnable">
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>
            <div class="stripe-div d-none col-12">
                <div class="row">
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('stripe_key', __('messages.stripe_key') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('stripe_key', $credentials['stripe_key'] ?? null, ['class' => 'form-control', 'id' => 'stripeKey', 'placeholder' => __('messages.stripe_key')]) }}

                    </div>
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('stripe_secret', __('messages.stripe_secret') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('stripe_secret', $credentials['stripe_secret'] ?? null, ['class' => 'form-control', 'id' => 'stripeSecret', 'placeholder' => __('messages.stripe_secret')]) }}
                    </div>
                </div>
            </div>

            {{-- PAYPAL --}}
            {{-- <div class="col-12 d-flex align-items-center">
                <span class="form-label my-3">{{ __('messages.paypal') . ' :' }}</span>
                <label class="form-check form-switch form-switch-sm ms-3">
                    <input type="checkbox" name="paypal_enable" class="form-check-input paypal-enable" value="1"
                        {{ !empty($credentials['paypal_enable']) == '1' ? 'checked' : '' }} id="paypalEnable">
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>
            <div class="paypal-div d-none  col-12">
                <div class="row">
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('paypal_client_id', __('messages.paypal_client_id') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('paypal_client_id', !empty($credentials['paypal_client_id']) ? $credentials['paypal_client_id'] : null, ['class' => 'form-control', 'id' => 'paypalKey', 'placeholder' => __('messages.paypal_client_id')]) }}
                    </div>
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('paypal_secret', __('messages.paypal_secret') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('paypal_secret', !empty($credentials['paypal_secret']) ? $credentials['paypal_secret'] : null, ['class' => 'form-control', 'id' => 'paypalSecret', 'placeholder' => __('messages.paypal_secret')]) }}
                    </div>
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('paypal_mode', __('messages.paypal_mode') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('paypal_mode', !empty($credentials['paypal_mode']) ? $credentials['paypal_mode'] : null, ['class' => 'form-control', 'id' => 'paypalMode', 'placeholder' => __('messages.paypal_mode')]) }}
                    </div>
                </div>
            </div> --}}

            {{-- Razorpay --}}
            <div class="col-12 d-flex align-items-center">
                <span class="form-label my-3">{{ __('messages.razorpay') . ' :' }}</span>
                <label class="form-check form-switch form-switch-sm ms-3">
                    <input type="checkbox" name="razorpay_enable" class="form-check-input razorpay_enable"
                        value="1" {{ !empty($credentials['razorpay_enable']) == '1' ? 'checked' : '' }}
                        id="razorpayEnable">
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>
            <div class="razorpay-div d-none col-12">
                <div class="row">
                    <div class="form-group col-sm-6 mb-5">

                        {{ Form::label('razorpay_key', __('messages.razorpay_key') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('razorpay_key', $credentials['razorpay_key'] ?? null, ['class' => 'form-control', 'id' => 'razorpayKey', 'placeholder' => __('messages.razorpay_key')]) }}

                    </div>
                    <div class="form-group col-sm-6 mb-5">
                        {{ Form::label('razorpay_secret', __('messages.razorpay_secret') . ':', ['class' => 'form-label']) }}
                        {{ Form::text('razorpay_secret', $credentials['razorpay_secret'] ?? null, ['class' => 'form-control', 'id' => 'razorpaySecret', 'placeholder' => __('messages.razorpay_secret')]) }}
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"
                    id="userCredentialSettingBtn">{{ __('messages.common.save') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
