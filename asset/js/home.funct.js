$(function () {

    $('.select2').select2({
        theme: 'bootstrap4'
    });

    /* Only Number Input ---------------------------------------- */
    $('#telpon').on('keypress', event => {
        const InputValue = /[0-9]/;
        if (!InputValue.test(String.fromCharCode(event.keyCode))) event.preventDefault();
    });

    /* Sekolah Combo Box --------------------------------------- */
    FillSekolah();
    $('#sekolah_not_found').click(function () {
        AnotherSekolah();
    });
    function FillSekolah() {
        let SekolahOption = '<option value="" disabled selected>Pilih Nama Sekolah</option>';
        Sekolah.forEach(sch => {
            SekolahOption += '<option value="' + sch['id'] + '">' + sch['sekolah'] + '</option>';
        });
        $('#sekolah').html(SekolahOption);
    }
    function AnotherSekolah() {
        ClearEffect('sekolah');
        $('#other_school').html('<input type="text" class="form-control" id="another_sekolah" name="another_sekolah" placeholder="Masukkan Nama Sekolah"><button type="button" class="btn btn-link" id="close_school"><i class="fas fa-times"></i></button>');
        $('#sekolah').html('').attr('disabled', true);
        $('#close_school').click(function () {
            Close_AnotherSekolah();
        });
        $('#another_sekolah').on('keyup', function () {
            ClearEffect('another_sekolah');
        });
    }
    function Close_AnotherSekolah() {
        ClearEffect('sekolah');
        $('#other_school').html('<small class="form-text text-muted ml-2">Jika tidak menemukan nama sekolah</small><button type="button" class="btn btn-xs btn-info ml-2" id="sekolah_not_found">Klik disini</button>');
        $('#sekolah').attr('disabled', false);
        FillSekolah();
        $('#sekolah_not_found').click(function () {
            AnotherSekolah();
        });
    }

    /* Adjusting Name ---------------------------------------- */
    const AdjustNama = Selector => {
        const Nama = $(Selector).val();
        let MyName = '';
        const SplitNama = Nama.trim().toLowerCase().split(/\s+/);
        SplitNama.forEach(names => {
            MyName += names.charAt(0).toUpperCase();
            if (names.length > 1) {
                MyName += names.slice(1);
            }
            MyName += ' ';
        });
        MyName = MyName.trim();
        $(Selector).val(MyName);
        return MyName;
    }

    const UpperName = Selector => {
        const Nama = $(Selector).val();
        let MyName = '';
        const SplitNama = Nama.trim().toLowerCase().split(/\s+/);
        SplitNama.forEach(names => {
            MyName += names.toUpperCase() + ' ';
        });
        MyName = MyName.trim();
        $(Selector).val(MyName);
        return MyName;
    }

    /* HP Validation ---------------------------------------- */
    const ValidHP = Selector => {
        const HapeVal = $(Selector).val();
        if (HapeVal == '' || HapeVal.substring(0, 2) != '08' || HapeVal.length < 10) {
            return false;
        } else {
            return true;
        }
    }

    /* Chehck Form ---------------------------------------- */
    $('#checkdata').on('change', function () {
        CheckData(this);
    });
    function CheckData(element) {
        if ($(element).is(':checked')) {
            if (InputValidation()) {
                $('#invalid_info').html('');
                $('#btnsave').attr('disabled', false);
            } else {
                $('#invalid_info').html('<small class="text-danger">mohon melengkapi data terlebih dahulu.</small>');
                $(element).prop('checked', false);
            }
        } else {
            $('#btnsave').attr('disabled', true);
        }
    }
    function InputValidation() {
        let Validation = true;
        if (AdjustNama('#nama') == '') { Validation = false; $('#nama').addClass('is-invalid'); }
        if (!ValidHP('#telpon')) { Validation = false; $('#telpon').addClass('is-invalid'); }
        if ($('#sekolah').val() == null) {
            const SekolahVal = $('#another_sekolah').val();
            if (SekolahVal == null) { Validation = false; $('#sekolah').addClass('is-invalid'); }
            if (SekolahVal == '') { Validation = false; $('#another_sekolah').addClass('is-invalid'); }
        }
        if (UpperName('#jurusan') == '') { Validation = false; $('#jurusan').addClass('is-invalid'); }
        const Kelamin = ($('#lakilaki').is(':checked') || $('#perempuan').is(':checked')) ? true : false;
        return (Validation && Kelamin) ? true : false;
    }

    /* Clear Effect Form ---------------------------------------- */
    $('input').on('keyup', function () {
        const ThisID = this.id;
        ClearEffect(ThisID);
    });

    $('select').on('change', function () {
        const ThisID = this.id;
        ClearEffect(ThisID);
    });

    const ClearEffect = id => {
        if ($('#' + id).hasClass('is-invalid')) $('#' + id).removeClass('is-invalid');
        if ($('#checkdata').is(':checked')) { $('#checkdata').prop('checked', false); $('#btnsave').attr('disabled', true); }
    }

});