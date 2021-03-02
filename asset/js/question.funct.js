$(function () {

    const GetQuestion = num => {
        $.get(BaseURL('api/question/' + num), function (data, status) {
            if (data['status'] && status == 'success') {
                let TextArea = '';
                data['data'].forEach(que => {
                    const dat = que['id'].slice(-1);
                    TextArea += '<div class="input-group mb-2"><div class="input-group-prepend"><span class="input-group-text"><strong>' + dat + '</strong>';
                    TextArea += '</span></div><textarea class="form-control" id="answer' + dat + '">' + que['question'] + '</textarea></div>';
                });
                TextArea += '<div class="text-center pt-2"><button class="btn btn-info btn-sm">Simpan Perubahan</button></div>';
                $('#Q' + num + 'BODY').html(TextArea);
            } else {
                ErrorAlert('Q' + num + 'BODY', 'Gagal Memuat Data - Error Code JS-15', 'Periksa koneksi internet dan muat ulang halaman!');
            }
        }).fail(function () {
            ErrorAlert('Q' + num + 'BODY', 'Gagal Memuat Data - Error Code JS-18', 'Periksa koneksi internet dan muat ulang halaman!');
        });
    }

    const ErrorAlert = (id, title, text) => {
        $('#' + id).html('<div class="alert alert-danger alert-dismissible my-auto">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            '<h5><i class="icon fas fa-exclamation-triangle mr-3"></i>' + title + '</h5><span>' + text + '</span></div>');
    }

    $('.qview').click(function () {
        if ($(this).data('views') == 0) {
            $(this).data('views', 1);
            $('#loading' + (this.id).replace('Q', '')).html('<i class="fas fa-2x fa-spinner fa-pulse text-primary"></i>');
            setTimeout(() => {
                GetQuestion((this.id).replace('Q', ''));
            }, 200);
        }
    });

});