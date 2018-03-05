$(document).ready(function() {
    $('#colorselector').colorselector({
        callback: function (value, color, title) {
            $("#colorValue").val(value);
            $("#colorColor").val(color);
            $("#colorTitle").val(title);
        }
    });

    $("#setColor").click(function (e) {
        $("#colorselector").colorselector("setColor", "#008B8B");
    });

    $("#setValue").click(function (e) {
        $("#colorselector").colorselector("setValue", 18);
    });
});
