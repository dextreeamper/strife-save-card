<?php
// Template Name: User Dashboard Payments Page
// Wp Estate Pack

//////////////////////////////////////////////////////////////////////////////////////////
// 3rd party login code
//////////////////////////////////////////////////////////////////////////////////////////

if( ( isset($_GET['code']) && isset($_GET['state']) ) ){
    estate_facebook_login($_GET);
}else if(isset($_GET['openid_mode']) && $_GET['openid_mode']=='id_res' ){
    estate_open_id_login($_GET);
}else if (isset($_GET['code'])){
    estate_google_oauth_login($_GET);
}else{
    if ( !is_user_logged_in() ) {
        wp_redirect(  esc_html( home_url() ) );exit();
    }
}

$current_user =   wp_get_current_user();
$userID       =   $current_user->ID;

$paid_submission_status         =   esc_html ( get_option('wp_estate_paid_submission','') );
$price_submission               =   floatval( get_option('wp_estate_price_submission','') );
$submission_curency_status      =   esc_html( get_option('wp_estate_submission_curency','') );
$edit_link                      =   wpestate_get_dasboard_add_listing();
$processor_link                 =   wpestate_get_procesor_link();
$options                        =   wpestate_page_details($post->ID);
get_header();
$first_name             =   get_the_author_meta( 'first_name' , $userID );
$last_name              =   get_the_author_meta( 'last_name' , $userID );
$email              =   get_the_author_meta( 'email' , $userID );
$accountEnding = '4903';

$args = array(
    'author'        =>  $userID,
    'post_status' => 'any',
    'post_type' => 'estate_property',
    'posts_per_page' => 1
    );
$query = new WP_Query( $args );
$userPosts = $query->post_count;
?>
</div>

<div class="row is_dashboard">
    <?php
    if( wpestate_check_if_admin_page($post->ID) ){
        if ( is_user_logged_in() ) {
//            get_template_part('templates/user_menu');
        }
    }
    ?>

    <div class="<?php /* dashboard-margin */ ?>">

        <div class="content_wrapper">

        <?php while (have_posts()) : the_post(); ?>
            <div class="padFormBotm"></div>
            <div class="titleInbox"><?php the_title(); ?></div>
            <div class="padFormBotm"></div>
            <?php //if( userPosts > 0 ) {
                if( 1 ) { ?>
            <div class="payout-info">
            <h5>Direct Deposit</h5>
            <div class="row">
            <p class="col-md-9">The account below is being used to issue your payouts for completed bookings. This information is securely sent to our payment provider Stripe and never stored by Uscout.</p>
            <div class="clear"></div>
            <div class="padFormBotm"></div>
            <div class="col-md-6">
                <div class="box1">
                    <div class="payName" id="auth_rep_name"></div>
                    Account ending in <span id="auth_acc_end"></span>
                    <div class="payLink"><a href="/payments/verify/">manage</a></div>
                </div>
                <div class="box1">
                    <div class="row">
                    <div class="payaccIcon col-xs-1"><i class="fa fa-times iconNo" aria-hidden="true"></i></div>
                    <div class="col-xs-9"><p>You cannot receive payouts until you add a bank account.</p></div>
                    </div>
                </div>
                <div class="box1">
                    <div class="row">
                    <div class="payaccIcon col-xs-1"><i class="fa fa-check iconYes" aria-hidden="true"></i></div>
                    <div class="col-xs-9"><p>Your account needs more information to be verified.</p></div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <?php } ?>

            <div class="clear"></div>
            <div class="padFormBotm"></div>
            <h5>Credit Cards</h5>
            <div class="row">
                <p class="col-md-9">Adding a credit card allows Uscout to charge you for reserved bookings. This information is securely sent to our payment provider and never stored by Uscout.</p>
                <div class="clear"></div>
                <form id="cc-form" name="cc" class="form-horizontal">
                    <div class="col-md-6">
                        <label class="labelBold">Add Credit Card</label>
                        <input type="text" data-stripe="name" name="cardholders_name" class="form-control " id="cardholders_name" placeholder="Cardholder's Name">
                        <input type="tel" data-stripe="number" name="cardnumber" class="form-control " id="cardnumber" placeholder="Card Number">
                        <input type="hidden" name="userID" class="form-control " id="userID" value="<?php echo $userID; ?>">
                        <input type="hidden" name="email" class="form-control " id="email" value="<?php echo $email; ?>">
                    </div>
                    <div class="clear"></div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-xs-4 half-padding-right">
                                <input type="text" data-stripe="exp_month" name="month" class="form-control " id="month" placeholder="MM">
                            </div>
                            <div class="col-xs-4 half-padding-right half-padding-left">
                                <input type="text" data-stripe="exp_year" name="year" class="form-control " id="year" placeholder="YYYY">
                            </div>
                            <div class="col-xs-4 half-padding-left">
                                <input type="text" data-stripe="cvv" name="cvv" class="form-control " id="cvv" placeholder="CVV">
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="col-md-6">
                        <input type="text" data-stripe="address_zip" name="zipcode" class="form-control " id="zipcode" placeholder="Zip / Postal code">
                        <button type="submit" class="wpb_btn-info wpb_btn-small wpestate_vc_button vc_button" id="add_card">Submit</button>
                        <p class="cta-disclaimer creditCard">By clicking <strong>Submit,</strong> you agree to <a href="/legal/terms">Uscout's Services Agreement</a>, which includes the <a href="/legal/terms/fees-overview">Fees Overview</a> and <a href="/legal/terms/community-guidelines">Community Guidelines</a>.</p>
                    </div>
                    <div id="processing"></div>
                    <div class="clear"></div>
                </form>
            </div>
            <div class="clear"></div>

        <?php endwhile; // end of the loop. ?>
           </div>
    </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function ($) {

    //var ajaxurl  =  ajaxcalls_vars.admin_url + 'admin-ajax.php';
    //var fieldname = ["name","bank_account"];

    //$.ajax({
    //     type: 'POST',
    //     url: ajaxurl,
    //     data: {
    //         'action'     :   'wpestate_ajax_get_stripe_account',
    //         'fieldname'  :   fieldname,
    //     },
    //     success: function (data) {
    //         var user_inf = JSON.parse(data);
    //         if(user_inf.error) {
    //             $('#payment_message').append(user_inf.error);
    //         } else {
    //             $('#auth_rep_name').append(user_inf.name);
    //             var acc_num = user_inf.bank_account;
    //             var lastFour = acc_num.substr(acc_num.length - 4);
    //             $('#auth_acc_end').append(lastFour);
    //         }
//            console.log(data);

    //     },
    //     error: function (errorThrown) {
    //        console.log(errorThrown);
    //     }
    // });



    $('#cc-form').submit(function(e){
        e.preventDefault();

        var ajaxurl  =  ajaxcalls_vars.admin_url + 'admin-ajax.php';
        var fieldname = ["userID":$('#userID').val(),"name":$('#cardholders_name').val(),"email":$('#email').val(),"number":$('#cardnumber').val(),"month":$('#month').val(),"year":$('#year').val(),"cvv":$('#cvv').val()];

        $.ajax({
         type: 'POST',
         url: ajaxurl,
         data: {
             'action'     :   'wpestate_ajax_set_stripe_account',
             'fieldname'  :   fieldname,
         },
         success: function (data) {
             //var user_inf = JSON.parse(data);
//            console.log(data);

         },
         error: function (errorThrown) {
            console.log(errorThrown);
         }
    });

        /*Stripe.setPublishableKey('pk_live_w5WHzz02bAjoVByBLfHI03vF');

        $('#add_card').attr("disabled", "disabled");
        $("#processing").html("Processing credit card...");     
            Stripe.createToken({
                number: $('#cardnumber').val(),
                cvc: $('#cvv').val(),
                exp_month: $('#month').val(),
                exp_year: $('#year').val(),
                name: $("#cardholders_name").val()
            }, stripeResponseHandler);

        function stripeResponseHandler(status, response) {
            if (response.error) {
                // re-enable the submit button
                $('#add_card').removeAttr("disabled");
                // show the errors on the form
                $("#processing").html(response.error.message);
            } else {
                $("#processing").html("Sending to payment page...");        
                var form$ = $("#cc-form");
                // token contains id, last4, and card type
                var token = response['id'];
                // insert the token into the form so it gets submitted to the server
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                // and submit
                form$.get(0).submit();
            }
        }*/
    });
});
</script>

<div>
<?php get_footer(); ?>
