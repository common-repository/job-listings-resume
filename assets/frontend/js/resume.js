jQuery(document).ready(function ($) {
    'use strict';

    // Resume skill animation
    $('.resume-skill-bar-percent').each(function () {
        $(this).animate({
            width: $(this).attr('data-percent')
        }, 3000);
    });

    // Delete resume detail item
    $('body').on('click', '.resume-detail-delete', function () {

        if (confirm(JLT_Resume.cfm_remove_resume_detail)) {
            $(this).closest('.field-repeat').remove();
        }

    });
    // Add resume detail item
    $(".resume-detail-add").on("click", function () {
        var parent = $(this).closest('.fieldset').find('.field');
        var template = parent.find('.field-clone-template').data('template');
        parent.append(template);
    });
});