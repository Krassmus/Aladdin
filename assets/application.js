
STUDIP.Aladdin = {
    "periodicalPushData": function () {
        if (jQuery(".subbrainstorms").length > 0) {
            return {
                "brainstorm_id": jQuery(".subbrainstorms").data("brainstorm_id"),
                "lasttime": jQuery(".subbrainstorms").data("lasttime")
            }
        }
    },
    "updateSubbrainstorms": function (output) {
        var ids = [];
        jQuery(".subbrainstorms > .brainstorm").each(function () {
            ids.push(jQuery(this).data("brainstorm_id"));
        });
        jQuery(".subbrainstorms").replaceWith(output.html);
        jQuery(".subbrainstorms > .brainstorm").each(function () {
            if (jQuery.inArray(jQuery(this).data("brainstorm_id"), ids) === -1) {
                jQuery(this).hide().fadeIn();
            }
        });
    },
    "vote_brainstorm": function () {
        var value = jQuery(this).val();
        var brainstorm_id = jQuery(this).closest(".brainstorm").data("brainstorm_id");
        jQuery(this).closest(".brainstorm").find("nav").css("opacity", "0.5");
        jQuery.ajax({
            "url": STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/aladdin/lamp/vote/" + brainstorm_id,
            "type": "post",
            "dataType": "json",
            "data": {
                "value": value
            },
            "success": function (output) {
                STUDIP.Aladdin.updateSubbrainstorms(output);
            }
        });
        return false;
    },
    "postBrainstorm": function () {
        var brainstorm_id = jQuery(this).find("input[name=range_id]").val();
        var text = jQuery(this).find("textarea").val();
        jQuery(this).find("textarea").val('');
        jQuery.ajax({
            "url": STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/aladdin/lamp/add_subbrainstorm/" + brainstorm_id,
            "type": "post",
            "dataType": "json",
            "data": {
                "text": text
            },
            "success": function (output) {
                STUDIP.Aladdin.updateSubbrainstorms(output);
            }
        });
    }
};
$(document).ready(function() {
    $('div.brainstorm textarea').autoResize();
});

jQuery(document).on("click", ".brainstorm form.voting input[type=image]", STUDIP.Aladdin.vote_brainstorm);