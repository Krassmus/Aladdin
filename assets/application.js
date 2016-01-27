
STUDIP.Aladdin = {
    "vote_brainstorm": function () {
        var value = jQuery(this).val();
        var brainstorm_id = jQuery(this).closest(".brainstorm").data("brainstorm_id");
        jQuery.ajax({
            "url": STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/aladdin/lamp/vote/" + brainstorm_id,
            "type": "post",
            "dataType": "json",
            "data": {
                "value": value
            },
            "success": function (output) {
                jQuery("#brainstorm_" + brainstorm_id).replaceWith(output.html);
            }
        });
        return false;
    }
};
$(document).ready(function() {
    $('div.brainstorm textarea').autoResize();
});

jQuery(document).on("click", ".brainstorm form.voting input[type=image]", STUDIP.Aladdin.vote_brainstorm);