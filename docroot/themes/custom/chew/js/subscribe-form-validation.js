/**
 * @file
 * Imlpement jQuery Validation for email subscribe form.
 */

(function ($, Drupal) {
  Drupal.behaviors.chewSubscribeFormValidation = {
    attach: function (context, settings) {
      $("#chewEmailSubscribeForm").validate({
        rules: {
          "First Name": {
            required: true
          },
          "Last Name": {
            required: true
          },
          "Email Address": {
            required: true,
            email: true
          }
        },
        messages: {
          "First Name": {
            required: "Please enter your first name."
          },
          "Last Name": {
            required: "Please enter your last name."
          },
          "Email Address": {
            required: "Please enter your email address.",
            email: "Please enter a valid email address."
          }
        },
        errorClass: "text-danger",
        submitHandler: function (form) {
          // Disable the submit button.
          $(form).find("button").attr("disabled", true);

          // Gather the subscribe form possible values.
          var firstName = $(form).find("input#firstName").val();
          var lastName = $(form).find("input#lastName").val();
          var email = $(form).find("input#emailAddress").val();

          // Find the hidden Drupal webform.
          var $webform = $(".webform-submission-email-subscriptions-form");

          // Populate Drupal webform values and submit.
          $webform.find("input[name='first_name']").val(firstName);
          $webform.find("input[name='last_name']").val(lastName);
          $webform.find("input[name='email_address']").val(email);
          $webform.submit();
        }
      });
    }
  };
})(jQuery, Drupal);
