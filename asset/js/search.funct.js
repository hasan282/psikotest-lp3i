$(function () {

    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $('#loading').fadeOut(function () {
        $(this).html('<i class="fas fa-2x fa-spinner fa-pulse text-primary"></i>');
    });

    FillSekolah();

    $('#tanggal').daterangepicker({
        singleDatePicker: true,
        autoApply: true,
        locale: {
            format: 'DD-MM-YYYY',
            daysOfWeek: ['Mn', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
        },
        opens: 'center',
        drops: 'up'
    }).val('');

    $('#clear_tanggal').click(function () {
        $('#tanggal').val('');
    });

    $('#clear_sekolah').click(function () {
        FillSekolah();
    });

    function FillSekolah() {
        let SekolahOption = '<option value="" disabled selected>Nama Sekolah</option>';
        Sekolah.forEach(sch => {
            SekolahOption += '<option value="' + sch['id'] + '">' + sch['sekolah'] + '</option>';
        });
        $('#sekolah').html(SekolahOption);
    }

    $('#btn_search').click(function () {
        const NamaVal = $('#nama').val();
        const TanggalVal = $('#tanggal').val();
        const SekolahVal = ($('#sekolah').val() == null) ? '' : $('#sekolah').val();
        const SearchURL = BaseURL('api/search?nm=' + encodeURI(NamaVal + '&sch=' + SekolahVal + '&tgl=' + TanggalVal));
        $('#loading').fadeIn(function () {
            GetSearch(SearchURL);
        });
    });

    const GetSearch = url => {
        $.get(url, function (data, status) {
            if (data['status'] && status == 'success') {
                let DataHasil = '';
                data['data'].forEach(res => {
                    const StatusTes = (res['num'] == res['jml']) ? ['success', 'Selesai'] : ['secondary', 'Belum Selesai'];
                    const OptionButton = (res['num'] == res['jml']) ? ['success', 'Lihat Hasil'] : ['info', 'Lanjutkan Tes'];
                    DataHasil += '<tr><td class="text-center">' + idToDate(res['id']) + '</td><td>' + res['nama'] + '</td><td class="text-center text-bold text-' + StatusTes[0] + '">' + StatusTes[1] + '</td><td class="text-center py-0 align-middle">';
                    DataHasil += '<button data-enc="' + res['encrypted'] + '" class="btn btn-sm continues btn-' + OptionButton[0] + ' py-0"><span class="text-bold">' + OptionButton[1] + '</span></button></td></tr>';
                });
                const TableResult = '<table class="table table-hover nowrap-table"><thead><tr><th class="text-center">Tanggal</th><th>Nama Peserta</th><th class="text-center">Proses Tes</th><th class="text-center"><i class="fas fa-cog"></i></th></tr></thead><tbody>' + DataHasil + '</tbody></table>';
                $('#search_result').addClass('table-responsive p-0').html(TableResult);
                $('#loading').fadeOut();
                $('.continues').click(function () {
                    toContinue($(this).data('enc'));
                });
            } else {
                $('#search_result').removeClass('table-responsive p-0').html('<div class="text-muted d-flex justify-content-center" style="min-height:40vh"><div class="text-center my-auto"><i class="fas fa-user-times fa-3x"></i><p class="mt-3">Tidak menemukan hasil pencarian</p></div></div>');
                $('#loading').fadeOut();
            }
        }).fail(function () {
            $('#search_result').removeClass('table-responsive p-0').html('<div class="text-muted d-flex justify-content-center" style="min-height:40vh"><div class="text-center my-auto"><i class="fas fa-exclamation-triangle fa-3x"></i><p class="mt-3 mb-0">Terjadi kesalahan</p><p class="mb-0">Periksa koneksi internet dan muat ulang halaman !</p></div></div>');
            $('#loading').fadeOut();
        });
    }

    const toContinue = val => {
        if (PARTIDAT === val) {
            window.location.href = BaseURL('test/result');
        } else {
            window.location.href = BaseURL('test/continuetest/' + val);
        }
    }

    const idToDate = id => {
        const Bulan = ('Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember').split('|');
        return id.slice(4, 6) + ' ' + Bulan[(id.slice(2, 4) - 1)] + ' 20' + id.slice(0, 2);
    }

});