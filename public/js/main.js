// https://stackoverflow.com/a/6029442
function resetForm(form) {
    // clearing inputs
    var inputs = form.getElementsByTagName('input');
    for (var i = 0; i<inputs.length; i++) {
        switch (inputs[i].type) {
            // case 'hidden':
            case 'text':
                inputs[i].value = '';
                break;
            case 'radio':
            case 'checkbox':
                inputs[i].checked = false;
        }
    }

    // clearing selects
    var selects = form.getElementsByTagName('select');
    for (var i = 0; i<selects.length; i++)
        selects[i].selectedIndex = 0;

    // clearing textarea
    var text= form.getElementsByTagName('textarea');
    for (var i = 0; i<text.length; i++)
        text[i].innerHTML= '';

    return false;
}
var spawned = 0;
$(function() {
    /*
    Filters Toggle Button (show / hide filter dialog)
    */

        $(document).click(function() {
            //var x = $("#homepage-filter-icon");
            //console.log("dentro" + $("#homepage-filter-icon").toString());

            if($("#homepage-mobile-filter-form").css('display') == 'flex'){
                if(spawned == 1 ){
                    spawned++;
                }
                else if(spawned == 2){
                    $("#homepage-mobile-filter-form").css({'display': 'none'});
                    spawned = 0;
                }

            }


        });
        $("#homepage-filter-icon").on('click', function(){
            x = $("#homepage-filter-icon");

            if($("#homepage-mobile-filter-form").css('display') == 'none') {
                $("#homepage-mobile-filter-form").css({'display': 'flex'});
                spawned = 1;
            }
            else {
                $("#homepage-mobile-filter-form").css({'display': 'none'});
                spawned = 0;
            }
            });







    /*
    BootStrap Color Selector Input
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
