$(function () {

    let PageNumber = 1;
    let MaxPage = 1;
    let ListView = 10;
    let DataFilter = '';

    FillSekolah();

    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $('#loading').fadeOut(function () {
        $(this).html('<i class="fas fa-2x fa-spinner fa-pulse text-primary"></i>');
        setTimeout(() => {
            $(this).fadeIn(function () {
                FillPeserta(PageNumber, ListView);
            });
        }, 100);
    });

    function FillPeserta(page, view) {
        $.get(BaseURL('api/peserta?page=' + page + '&list=' + view + DataFilter), function (data, status) {
            if (data.status && status == 'success') {
                setMaxPage(data.total);
                let TablePeserta = '';
                data.data.forEach(val => {
                    TablePeserta += '<tr><td class="text-center">' + val.tanggal + '</td><td id="nama_' + val.id + '">' + val.nama + '</td><td>' + val.sekolah + '</td>';
                    TablePeserta += '<td class="text-center text-bold text-' + ((val.proses == 'Selesai') ? 'success' : 'secondary') + '">' + val.proses + '</td><td class="text-center">' + val.karakter + '</td>';
                    TablePeserta += '<td class="text-center"><button id="detail_' + val.id + '" class="btn btn-info ps-detail py-0 px-2" data-toggle="modal" data-target="#detailmodal"><i class="far fa-eye"></i></button>&nbsp;&nbsp;<button id="del_' + val.id + '" class="btn btn-danger py-0 btn-delete"><i class="fas fa-times"></i></button></td></tr>';
                });
                const TableView = '<table class="table table-hover nowrap-table"><thead><tr><th class="text-center">Tanggal</th><th>Nama Peserta</th><th>Asal Sekolah</th><th class="text-center">Proses Tes</th><th class="text-center">Karakter</th><th class="text-center">Option</th></tr></thead><tbody>' + TablePeserta + '</tbody></table>';
                $('#list_peserta').addClass('table-responsive p-0').html(TableView);
                $('#total_data').html(data.total);
                $('#halaman').html(PageNumber);
                $('#data_top').html(((PageNumber - 1) * ListView) + 1);
                $('#data_bottom').html(((PageNumber - 1) * ListView) + data.data.length);
                $('#loading').fadeOut();
                EnableButton();
                $('.ps-detail').click(function () {
                    const detailID = this.id.replace('detail_', '');
                    ShowDetail(detailID);
                });
                $('.btn-delete').click(function () {
                    const delID = this.id.replace('del_', '');
                    DeletePeserta(delID, $('#nama_' + delID).html());
                });
            } else {
                FailedLoad('fa-user-times', 'Data tidak ditemukan', 'tidak ada data yang dapat ditampilkan');
            }
        }, 'json').fail(function () {
            FailedLoad();
        });
    }

    function setMaxPage(datacount) {
        const Pages = Math.floor(datacount / ListView);
        MaxPage = (datacount % ListView == 0) ? Pages : Pages + 1;
    }

    function FailedLoad(icon = 'fa-exclamation-triangle', str1 = 'Gagal memuat data', str2 = 'Periksa koneksi internet dan muat ulang halaman') {
        $('#list_peserta').removeClass('table-responsive p-0').html('<div class="text-muted d-flex justify-content-center" style="min-height:40vh"><div class="text-center my-auto"><i class="fas ' + icon + ' fa-3x"></i><p class="mt-3 mb-0">' + str1 + '</p><p class="mb-0">' + str2 + ' !</p></div></div>');
        $('#loading').fadeOut();
        $('#total_data').html('0');
        $('#halaman').html('1');
        $('#data_top').html('0');
        $('#data_bottom').html('0');
        $('.data-nav').attr('disabled', true);
    }

    $('.data-nav').click(function () {
        const Order = $(this).data('page');
        $('.data-nav').attr('disabled', true);
        switch (Order) {
            case 'first':
                PageNumber = 1;
                break;
            case 'last':
                PageNumber = MaxPage;
                break;
            case 'prev':
                PageNumber--;
                break;
            case 'next':
                PageNumber++;
                break;
            default:
                PageNumber = 1;
                break;
        }
        $('#loading').fadeIn(function () {
            FillPeserta(PageNumber, ListView);
        });
    });

    function EnableButton() {
        if (PageNumber < MaxPage) {
            $('button[data-page="next"]').attr('disabled', false);
            $('button[data-page="last"]').attr('disabled', false);
        }
        if (PageNumber > 1) {
            $('button[data-page="prev"]').attr('disabled', false);
            $('button[data-page="first"]').attr('disabled', false);
        }
    }

    function ShowDetail(pid) {
        $('#show_detail').html('<div class="text-center text-info pb-4"><i class="fas fa-2x fa-spinner fa-pulse text-primary"></i></div>');
        setTimeout(() => {
            $.get(BaseURL('api/peserta?id=' + pid), function (data, status) {
                if (data.status && status == 'success') {
                    const Detail = '<table class="table table-bordered table-striped"><tr><td class="bg-dark text-bold" style="width:35%">Nama Lengkap</td><td class="align-middle py-0"><span class="d-flex justify-content-between align-items-center">' +
                        '<p id="detail_name" class="my-0 text-bold">' + data.data.nama + '</p><button id="btn_copy_name" class="btn btn-default btn-sm py-1">Copy</button></span></td></tr><tr><td class="bg-dark text-bold">Jenis Kelamin</td>' +
                        '<td class="align-middle">' + data.data.kelamin + '</td></tr><tr><td class="bg-dark text-bold">Nomor Telpon</td><td class="align-middle py-0"><span class="d-flex justify-content-between align-items-center">' +
                        '<p id="detail_phone" class="my-0 text-bold">' + data.data.telpon + '</p><button id="btn_copy_phone" class="btn btn-default btn-sm py-1">Copy</button></span></td></tr><tr><td class="bg-dark text-bold">Asal Sekolah</td>' +
                        '<td class="align-middle">' + data.data.sekolah + '</td></tr><tr><td class="bg-dark text-bold">Jurusan</td><td>' + data.data.jurusan + '</td></tr><tr><td class="bg-dark text-bold">Tanggal Pendaftaran</td><td class="align-middle">' + data.data.tanggal + '</td></tr>' +
                        '<tr><td class="bg-dark text-bold">Proses Tes</td><td>' + data.data.proses + '</td></tr><tr><td class="bg-dark text-bold">Karakter</td><td>' + ((data.data.karakter == null) ? '-' : data.data.karakter) + '</td></tr></table><div class="row mx-auto" style="max-width:600px"><div class="col-6 col-sm-3"><div class="card card-danger bg-dark">' +
                        '<div class="card-header py-2 d-flex justify-content-center"><h5 class="card-title text-bold">A</h5></div><div class="card-body py-3 text-center"><h4 class="text-bold mb-0">' + data.data.answer_a + '</h4></div></div></div><div class="col-6 col-sm-3">' +
                        '<div class="card card-warning bg-dark"><div class="card-header py-2 d-flex justify-content-center"><h5 class="card-title text-bold">B</h5></div><div class="card-body py-3 text-center"><h4 class="text-bold mb-0">' + data.data.answer_b + '</h4></div></div></div>' +
                        '<div class="col-6 col-sm-3"><div class="card card-info bg-dark"><div class="card-header py-2 d-flex justify-content-center"><h5 class="card-title text-bold">C</h5></div><div class="card-body py-3 text-center"><h4 class="text-bold mb-0">' + data.data.answer_c + '</h4></div></div></div>' +
                        '<div class="col-6 col-sm-3"><div class="card card-success bg-dark"><div class="card-header py-2 d-flex justify-content-center"><h5 class="card-title text-bold">D</h5></div><div class="card-body py-3 text-center"><h4 class="text-bold mb-0">' + data.data.answer_d + '</h4></div></div></div></div>';
                    $('#show_detail').html(Detail);
                    $('#btn_copy_name').click(function () {
                        CopyToClipboard($('#detail_name').html());
                    });
                    $('#btn_copy_phone').click(function () {
                        CopyToClipboard($('#detail_phone').html());
                    });
                    $('#edit_link').html('<a href="' + BaseURL('parti/edit/' + data.data.id) + '" class="btn btn-secondary"><i class="fas fa-edit mr-2"></i>Ubah Data</a>');
                } else {
                }
            }, 'json').fail(function () {
            });
        }, 100);
    }

    function DeletePeserta(pid, nama) {
        Swal.fire({
            icon: 'warning',
            title: '<div>Hapus data peserta<br><small>' + nama + '</small><div>',
            text: 'Data yang dihapus tidak dapat dikembalikan',
            showCancelButton: true,
            confirmButtonText: 'HAPUS DATA',
            cancelButtonText: 'BATALKAN',
            confirmButtonColor: '#DF4759'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = BaseURL('parti/delete/' + pid);
            }
        });
    }

    function CopyToClipboard(text) {
        let textArea = document.createElement('textarea');
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = 0;
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            const successful = document.execCommand('copy');
            const msg = successful ? 'copied' : 'not copied';
            console.log(text + ' is ' + msg);
        } catch (err) {
            console.log('failed to copy');
        }
        document.body.removeChild(textArea);
    }

    $('#clear_sekolah').click(function () {
        FillSekolah();
    });

    function FillSekolah() {
        let SekolahOption = '<option value="" disabled selected>Semua Sekolah</option>';
        Sekolah.forEach(sch => {
            SekolahOption += '<option value="' + sch['id'] + '">' + sch['sekolah'] + '</option>';
        });
        $('#sekolah').html(SekolahOption);
    }

    $('#btn_filter').click(function () {
        $('.data-nav').attr('disabled', true);
        const Nama = '&nama=' + $('#nama').val();
        const Sekolah = '&sekolah=' + (($('#sekolah').val() == null) ? '' : $('#sekolah').val());
        const Proses = 'proses=' + $('#prosestes').val();
        DataFilter = encodeURI('&' + Nama + '&' + Sekolah + '&' + Proses);
        PageNumber = 1;
        ListView = 10;
        $('#loading').fadeIn(function () {
            FillPeserta(PageNumber, ListView);
        });
    });

    $('#btn_clear').click(function () {
        $('.data-nav').attr('disabled', true);
        $('#nama').val('');
        $('#prosestes').val('2');
        $('#clear_sekolah').trigger('click');
        PageNumber = 1;
        ListView = 10;
        DataFilter = '';
        $('#loading').fadeIn(function () {
            FillPeserta(PageNumber, ListView);
        });
    });

});