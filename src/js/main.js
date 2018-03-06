$(function() {
    $("#homepage-mobile-filters").hide();

    $("#homepage-filter-icon").on('click', function(){
        $("#homepage-mobile-filters").toggle();
    });

    /*
    * BootStrap Color Selector Input
    */
    $('#colorselector').colorselector({
        callback: function (value, color, title) {
            $("#colorValue").val(value);
            $("#colorColor").val(color);
            $("#colorTitle").val(title);
        }
    });

    $("#setColor").on('click', function () {
        $("#colorselector").colorselector("setColor", "#008B8B");
    });

    $("#setValue").on('click', function () {
        $("#colorselector").colorselector("setValue", 18);
    });
});
