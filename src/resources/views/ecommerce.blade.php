<div class="row">
    <div class="col-md-12">
        @php \Actions::do_action('pre_fac_checkout_form',$gateway) @endphp
        <h5>Enter your card details</h5>
        <form id="payment-form" action="{{ url($action) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    {!! CoralsForm::text('payment_details[number]','Fac::attributes.card_number',true,'',['maxlength'=>16,'id'=>'fac_number']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    {!! CoralsForm::select('payment_details[expiryMonth]', 'Fac::attributes.expMonth', \Payments::expiryMonth(), true,now()->format('m')) !!}
                </div>
                <div class="col-md-4">
                    {!! CoralsForm::select('payment_details[expiryYear]', 'Fac::attributes.expYear', \Payments::expiryYear(), true,now()->format('Y')) !!}
                </div>
                <div class="col-md-4">
                    {!! CoralsForm::text('payment_details[cvv]','Fac::attributes.cvv', true,'',['placeholder'=>"CCV", 'maxlength'=>4,'id'=>'fac_cvv']) !!}
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $('#payment-form').on('submit', function (event) {
        event.preventDefault();

        $form = $('#payment-form');
        $form.find('input[type=text]').empty();
        $form.append("<input type='hidden' name='checkoutToken' value='Fac'/>");
        $form.append("<input type='hidden' name='gateway' value='Fac'/>");
        ajax_form($form);
    });
</script>
