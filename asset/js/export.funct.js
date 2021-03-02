$(document).ready(function () {

    $('.checkcolumns').change(function () {
        const CheckID = this.id;
        if ($(this).is(':checked')) {
            $('label[for="' + CheckID + '"]').removeClass('text-gray-dark').addClass('text-primary');
            if ($('.checkcolumns:checked').length == $('.checkcolumns').length) $('#check_all').prop('checked', true);
        } else {
            $('label[for="' + CheckID + '"]').removeClass('text-primary').addClass('text-gray-dark');
            $('#check_all').prop('checked', false);
        }
        if ($('.checkcolumns:checked').length < 1) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    });

    $('input[name="rows"]').change(function () {
        $('.labelrows').removeClass('text-primary').addClass('text-gray-dark');
        $('label[for="row' + $(this).val() + '"]').removeClass('text-gray-dark').addClass('text-primary');
    });

    $('input[name="phones"]').change(function () {
        $('.labelphone').removeClass('text-primary').addClass('text-gray-dark');
        $('#label' + this.id).removeClass('text-gray-dark').addClass('text-primary');
    });

    $('#check_telpon').change(function () {
        if ($(this).is(':checked')) {
            $('#phoneformat').attr('style', 'height:auto;overflow:hidden');
        } else {
            $('#phoneformat').attr('style', 'height:0px;overflow:hidden');
        }
    });

    $('#check_all').change(function () {
        if ($(this).is(':checked')) {
            $('.checkcolumns:not(:checked)').each(function () {
                $(this).trigger('click');
            });
        } else {
            $('.checkcolumns:checked').each(function () {
                $(this).trigger('click');
            });
        }
    });

    function TriggerCheck(ids = []) {
        ids.forEach(id => {
            $('#' + id).trigger('click');
        });
    }

    TriggerCheck(['check_nama', 'row50', 'phone08']);

});