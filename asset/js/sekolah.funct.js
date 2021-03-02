$(function () {

    FillSekolah('Sekolah');
    FillSekolahTemp();
    FillSekolahCombo();

    $('#sekolah').keyup(function () {
        $(this).removeClass('is-valid').removeClass('is-invalid');
        $('#btn_save').attr('disabled', true);
        if ($(this).data('trigger') == 0) {
            LoadProvinsi();
            $(this).data('trigger', '1');
        };
    });

    $('#provinsi').change(function () {
        EmptyOption('kabupaten');
        EmptyOption('kecamatan');
        FillOption('kabupaten', $(this).val());
    });

    $('#kabupaten').change(function () {
        EmptyOption('kecamatan');
        FillOption('kecamatan', $(this).val());
    });

    function FillSekolahCombo() {
        let Options = '<option selected disabled>Nama Sekolah</option>';
        Sekolah.forEach(sch => {
            Options += '<option value="' + sch['id'] + '">' + sch['sekolah'] + '</option>';
        });
        $('#com_sekolah').html(Options).attr('disabled', false);
    }

    function LoadProvinsi() {
        $('#loading_provinsi').html('<i class="fas fa-spinner fa-pulse"></i>');
        $('#message_provinsi').html('');
        $.get(BaseURL('data/address?area=provinsi'), function (data, status) {
            if (data.status && status == 'success') {
                let ProvinsiOpt = '<option selected disabled>pilih provinsi</option>';
                $.each(data.data, function (key, val) {
                    ProvinsiOpt += '<optgroup id="opt_' + key + '" label="' + val.kelompok + '">';
                    $.each(val.provinsi, function (id, nama) {
                        ProvinsiOpt += '<option value="' + id + '">' + nama + '</option>';
                    });
                    ProvinsiOpt += '</optgroup>';
                });
                $('#provinsi').html(ProvinsiOpt).attr('disabled', false);
                $('#loading_provinsi').html('');
            } else {
                FailedLoading('provinsi');
            }
        }, 'json').fail(function () {
            FailedLoading('provinsi');
        });
    }

    const FillOption = (area, key) => {
        $('#loading_' + area).html('<i class="fas fa-spinner fa-pulse"></i>');
        $('#message_' + area).html('');
        setTimeout(() => {
            $.get(BaseURL('data/address?area=' + area + '&id=' + key), function (data, status) {
                if (data.status && status == 'success') {
                    let Options = '<option selected disabled>pilih ' + area + '</option>';
                    data.data.forEach(val => {
                        Options += '<option value="' + val[area + '_id'] + '">' + val[area] + '</option>';
                    });
                    $('#' + area).html(Options).attr('disabled', false);
                    $('#loading_' + area).html('');
                } else {
                    FailedLoading(area);
                }
            }, 'json').fail(function () {
                FailedLoading(area);
            });
        }, 100);
    }

    const EmptyOption = area => {
        $('#' + area).html('').attr('disabled', true);
        $('#' + area).removeClass('is-valid').removeClass('is-invalid');
        $('#btn_save').attr('disabled', true);
    }

    const FailedLoading = region => {
        $('#loading_' + region).html('');
        $('#message_' + region).html('tidak dapat memuat data ' + region);
    }

    $('.is-require').change(function () {
        $(this).removeClass('is-valid').removeClass('is-invalid');
    });

    $('#btn_check').click(function () {
        let Required = true;
        const Sekolah = $('#sekolah').val();
        $('#sekolah').val(Sekolah.toUpperCase());
        $('.is-require').each(function () {
            if ($(this).val() == null || $(this).val() == '') {
                $(this).removeClass('is-valid').addClass('is-invalid');
                Required = false;
            } else {
                $(this).addClass('is-valid').removeClass('is-invalid');
            }
        });
        if (Required) $('#btn_save').attr('disabled', false);
    });

    function DeleteSekolah(button) {
        const btnid = button.id;
        const sekolah = $('#' + btnid.replace('btn', 'sch')).html();
        const peserta = $('#' + btnid.replace('btn', 'jml')).html();
        if (peserta == 0) {
            Swal.fire({
                icon: 'warning',
                title: '<div>Hapus data sekolah<br><small>' + sekolah + '</small><div>',
                text: 'Data yang dihapus tidak dapat dikembalikan',
                showCancelButton: true,
                confirmButtonText: 'HAPUS DATA',
                cancelButtonText: 'BATALKAN',
                confirmButtonColor: '#DF4759'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = BaseURL('school/delete/' + btnid.replace('btn_', ''));
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '<div>Tidak dapat menghapus<br><small>' + sekolah + '</small></div>',
                html: 'Sekolah ini memiliki <strong>' + peserta + '</strong> data peserta'
            });
        }
    }

    function FillSekolah(sortby, namafilter = null) {
        let DataSekolah;
        if (namafilter === null) {
            DataSekolah = JSON.parse(JSON.stringify(Sekolah));
        } else {
            DataSekolah = Sekolah.filter(function (sch) {
                const NamaSekolah = sch.sekolah.toLowerCase();
                return NamaSekolah.includes(namafilter.toLowerCase());
            });
        }
        let Table = '';
        if (sortby == 'Peserta') {
            DataSekolah.sort((a, b) => {
                return b.jumlah - a.jumlah
            });
        }
        let NumberIndex = 1;
        DataSekolah.forEach(rows => {
            Table += '<tr><td class="text-center text-bold">' + (NumberIndex++) + '</td><td id="sch_' + rows.id + '">' + rows.sekolah + '</td><td class="text-center" id="jml_' + rows.id + '">' + rows.jumlah + '</td>';
            Table += '<td class="text-center"><a href="' + BaseURL('school/detail/') + rows.id + '" class="btn btn-info py-0 px-2"><i class="far fa-eye"></i></a>';
            Table += '&nbsp;&nbsp;<button id="btn_' + rows.id + '" class="btn btn-danger py-0 button-delete"><i class="fas fa-times"></i></button></td></tr>';
        });
        $('#list_sekolah').html(Table);
        $('.button-delete').click(function () {
            DeleteSekolah(this);
        });
    }

    $('input[name="sort"]').change(function () {
        const FilterVal = ($('#table_search').val() == '') ? null : $('#table_search').val();
        FillSekolah($(this).val(), FilterVal);
    });

    $('#table_search').keyup(function () {
        const ToSearch = ($(this).val() == '') ? null : $(this).val();
        const SortBy = $('input[name="sort"]:checked').val();
        FillSekolah(SortBy, ToSearch);
    });

    $('#clear_search').click(function () {
        $('#table_search').val('').trigger('keyup');
    });

    $('#check_all').change(function () {
        if ($(this).is(':checked')) {
            $('.check-temp').prop('checked', true);
        } else {
            $('.check-temp').prop('checked', false);
        }
        CheckSekolahTemp();
    });

    $('.check-temp').change(function () {
        if ($(this).is(':checked')) {
            if ($('.check-temp:checked').length == $('.check-temp').length) $('#check_all').prop('checked', true);
        } else {
            $('#check_all').prop('checked', false);
        }
        CheckSekolahTemp();
    });

    $('#com_sekolah').change(function () {
        CheckSekolahTemp();
    });

    function CheckSekolahTemp() {
        let TempValue = new Array;
        $('.check-temp:checked').each(function () {
            TempValue.push($(this).val());
        });
        $('#merge_sekolah').val(TempValue.join('@'));
        if ($('#com_sekolah').val() == null || $('#merge_sekolah').val() == '') {
            $('#save_temp').attr('disabled', true);
        } else {
            $('#save_temp').attr('disabled', false);
        }
    }

    function FillSekolahTemp() {
        let Table = '';
        SekolahTemp.forEach(stm => {
            Table += '<tr><td>' + stm.sekolah + '</td><td class="text-center py-0 align-middle"><div class="icheck-primary">';
            Table += '<input type="checkbox" class="check-temp" id="check_' + stm.id + '" value="' + stm.id + '">';
            Table += '<label for="check_' + stm.id + '"></label></div></td></tr>';
        });
        $('#list_temp_sekolah').html(Table);
    }

});