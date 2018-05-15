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
                inputs[i].checked = true;
                break;
            case 'date':
                inputs[i].value = '';
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

$(function() {
    /*
    Filters Toggle Button (show / hide filter dialog)
    */
    $("#homepage-filter-icon").on('click', function(){
        if($("#homepage-mobile-filters").css('display') == 'none')
            $("#homepage-mobile-filters").css({'display': 'table'});
        else
            $("#homepage-mobile-filters").css({'display': 'none'});
    });

    /*
    Select/Deselect all buttons
    */
    $("#new-select-all").on('click', function(){
        $("input[name='comuniDestinatari[]']").each( function () {
            this.checked=true;
        });
    });

    $("#new-select-none").on('click', function(){
        $("input[name='comuniDestinatari[]']").each( function () {
            this.checked=false;
        });
    });
});
