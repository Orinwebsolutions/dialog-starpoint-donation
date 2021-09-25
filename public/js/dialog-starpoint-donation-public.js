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
    jQuery("#stardonate").click(function (e) {
      e.preventDefault();

      jQuery("#messages").html("").removeClass("success warning");
      jQuery("#stardonation_form").addClass("processing");

      let step = jQuery("#stardonation_form").find('input[name="step"]').val();
      let balance_amount = parseInt(
        jQuery("#stardonation_form").find('input[name="balance_amount"]').val()
      );
      let amount = parseInt(
        jQuery("#stardonation_form").find('input[name="amount"]').val()
      );
      if (
        (balance_amount < amount || amount <= 0 || balance_amount <= 0) &&
        step == 2
      ) {
        if (balance_amount < amount) {
          jQuery("#messages").addClass("warning");
          jQuery("#messages").html(
            "Can't proceed with it please reduce amount"
          );
          jQuery(this).prop("disabled", false);
          jQuery("#stardonation_form").removeClass("processing");
        } else if (amount <= 0) {
          jQuery("#messages").html("Please enter Starpoints redeem amount");
          jQuery(this).prop("disabled", false);
        } else if (balance_amount <= 0) {
          jQuery("#messages").html(
            "Can't proceed now you dont have enough balance with you."
          );
        }
      } else {
        ajaxCall(jQuery("#stardonation_form").serialize());
      }
      // }
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
          }

          if (response.type == "pinSend") {
            jQuery("#stardonation_form").find('input[name="step"]').val(3);
            jQuery("#balance_retrieve_form").css("display", "none");
            jQuery("#otp_confirmation_form").css("display", "block");
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
          }
        },
      });
    }
  });
})(jQuery);
