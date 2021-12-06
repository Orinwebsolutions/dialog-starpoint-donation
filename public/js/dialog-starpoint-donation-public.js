(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  jQuery(document).ready(function () {
    let step;
    jQuery("#stardonateback").css("display", "none");
    jQuery("#stardonate").click(function (e) {
      e.preventDefault();

      jQuery("#messages").html("").removeClass("success warning");
      jQuery("#stardonation_form").addClass("processing");

      step = jQuery("#stardonation_form").find('input[name="step"]').val();
      let balance_amount = parseInt(
        jQuery("#stardonation_form").find('input[name="balance_amount"]').val()
      );
      let amount = parseInt(
        jQuery("#stardonation_form").find('input[name="amount"]').val()
      );
      let rednumber = parseInt(
        jQuery("#stardonation_form").find('input[name="redeem-number"]').val()
      );

      if (
        (balance_amount < amount ||
          amount <= 0 ||
          balance_amount <= 0 ||
          isNaN(amount)) &&
        step == 2
      ) {
        if (balance_amount < amount) {
          jQuery("#messages").addClass("warning");
          jQuery("#messages").html(
            "It is not possible to redeem more than the Redeemable Amount. Please reduce the Redeem Amount"
          );
          jQuery(this).prop("disabled", false);
          jQuery("#stardonation_form").removeClass("processing");
        } else if (amount <= 0) {
          jQuery("#messages").addClass("warning");
          jQuery("#messages").html("Please enter Starpoints redeem amount");
          jQuery("#stardonation_form").removeClass("processing");
          jQuery(this).prop("disabled", false);
        } else if (balance_amount <= 0) {
          jQuery("#messages").html(
            "Unable to proceed with donation! You don’t have enough redeemable Star Points balance with you."
          );
          jQuery("#messages").addClass("warning");
          jQuery("#stardonation_form").removeClass("processing");
          jQuery(this).prop("disabled", false);
        } else if (isNaN(amount)) {
          jQuery("#messages").addClass("warning");
          jQuery("#messages").html(
            "Unable to proceed with the donation! Please enter Star Points redeem amount"
          );
          jQuery("#stardonation_form").removeClass("processing");
          jQuery(this).prop("disabled", false);
        }
      } else {
        if (!rednumber) {
          jQuery("#messages").addClass("warning");
          jQuery("#messages").html(
            "We are unable proceed without redeemable phone number"
          );
          jQuery("#stardonation_form").removeClass("processing");
          jQuery("#stardonate").prop("disabled", false);
        } else {
          console.log("else");
          ajaxCall(jQuery("#stardonation_form").serialize());
        }
      }
      // }
    });
    jQuery("#stardonateback").click(function (e) {
      jQuery("#messages").html("").removeClass("success warning");
      step = jQuery("#stardonation_form").find('input[name="step"]').val();
      if (step == 2) {
        jQuery("#stardonation_form").find('input[name="step"]').val(1);
        jQuery("#otp_confirmation_form").css("display", "none");
        jQuery("#balance_retrieve_form").css("display", "none");
        jQuery("#balance_inquire_form").css("display", "block");
        jQuery("#stardonateback").css("display", "none");
      } else if (step == 3) {
        jQuery("#stardonation_form").find('input[name="step"]').val(2);
        jQuery("#balance_inquire_form").css("display", "none");
        jQuery("#otp_confirmation_form").css("display", "none");
        jQuery("#balance_retrieve_form").css("display", "block");
      }
    });

    function ajaxCall(data) {
      jQuery.ajax({
        type: "post",
        dataType: "json",
        url: starAjax.ajaxurl,
        data: { action: "starpoint_ajax", data: data },
        success: function (response) {
          console.log(response);
          jQuery("#stardonation_form").removeClass("processing");
          jQuery("#messages").removeClass("success warning");

          if (response.type == "error") {
            jQuery("#messages").addClass("warning");
            jQuery("#messages").html(response.msg);
          }

          if (response.type == "auth&bal") {
            jQuery("#stardonation_form")
              .find('input[name="accessToken"]')
              .val(response.auth);
            jQuery("#stardonation_form").find('input[name="step"]').val(2);
            jQuery("#stardonate").prop("disabled", false);
            jQuery("#balance_inquire_form").css("display", "none");
            jQuery("#balance_retrieve_form").css("display", "block");
            jQuery("#stardonation_form")
              .find('input[name="balance_amount"]')
              .val(response.redeemableBalance);
            jQuery("#stardonateback").css("display", "block");
          }

          if (response.type == "pinSend") {
            jQuery("#stardonation_form").find('input[name="step"]').val(3);
            jQuery("#balance_retrieve_form").css("display", "none");
            jQuery("#otp_confirmation_form").css("display", "block");
            jQuery("#stardonateback").css("display", "block");
          }
          if (response.type == "burnstarpoint") {
            jQuery(
              '#stardonation_form input[type="text"], #stardonation_form input[type="email"]'
            ).each(function () {
              jQuery(this).val("");
            });
            jQuery("#stardonation_form").find('input[name="step"]').val(1);
            jQuery("#stardonation_form")
              .find('input[name="accessToken"]')
              .val("");
            jQuery("#otp_confirmation_form").css("display", "none");
            jQuery("#balance_inquire_form").css("display", "block");
            jQuery("#messages").addClass("success");
            jQuery("#messages").html("Thanks for your valuable donation");
            jQuery("#stardonateback").css("display", "none");
          }
        },
      });
    }
  });
})(jQuery);
