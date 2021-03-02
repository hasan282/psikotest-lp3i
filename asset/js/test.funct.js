let Answer = null;
let QuestNum = $('.progress').data('current');

const getQuestion = que => {
    $.get(BaseURL('api/question/' + que), function (data, status) {
        if (data['status'] && status == 'success') {
            let Question = [];
            data['data'].forEach(opt => {
                Question.push('<div class="callout callout-success p-cursor" data-ans="' + opt['id'].slice(-1) + '"><p>' + opt['question'] + '</p></div>');
            });
            $('#question').html(ArrayShuffle(Question).join(''));
            $('#loading').fadeOut();
            ChangeProgress(QuestNum);
            $('.callout-success').click(function () {
                $('.callout-success').removeClass('bg-gradient-success');
                $(this).addClass('bg-gradient-success');
                Answer = $(this).data('ans');
                if (Answer != null) $('#btn_next').attr('disabled', false);
            });
        } else {
            if (QuestNum - 1 == $('.progress').data('maxquest')) {
                $('#question').html('<div class="text-center"><h5>Terima Kasih</h5>' +
                    '<h3 class="text-bold">Semua pertanyaan sudah dijawab.</h3>' +
                    '<p class="text-info">Sekarang kamu bisa melihat hasilnya</p>' +
                    '</div><div class="text-center"><a href="' + BaseURL('test/result') + '" class="btn btn-lg btn-info text-bold">' +
                    'Lihat Hasil Tes</a></div>');
                ChangeProgress(QuestNum - 1);
                $('#loading').fadeOut();
            } else {
                ErrorAlert('Terjadi Kesalahan - Error Code JS-30', 'Periksa koneksi internet dan muat ulang halaman!');
            }
        }
    }).fail(function () {
        ErrorAlert('Gagal Memuat Data - Error Code JS-38', 'Periksa koneksi internet dan muat ulang halaman!');
    });
}

const ArrayShuffle = $array => {
    let ctr = $array.length, temp, index;
    while (ctr > 0) {
        index = Math.floor(Math.random() * ctr); ctr--;
        temp = $array[ctr]; $array[ctr] = $array[index];
        $array[index] = temp;
    }
    return $array;
}

const ChangeProgress = current => {
    $('.current').html(current);
    const Percentage = Math.round(current / $('.progress').data('maxquest') * 100);
    $('.progress-bar').attr('style', 'width:' + Percentage + '%');
}

const SendAnswer = (num, ans) => {
    $.post(BaseURL('api/answer/') + $('#peserta_data').data('id'), {
        number: num, answer: ans
    }, function (data) {
        if (data.status) {
            QuestNum = data.number;
            getQuestion(QuestNum);
        } else {
            ErrorAlert('Terjadi Kesalahan - Error Code JS-64', 'Periksa koneksi internet dan muat ulang halaman!');
        }
    }).fail(function () {
        ErrorAlert('Gagal Memuat Data - Error Code JS-67', 'Periksa koneksi internet dan muat ulang halaman!');
    });
}

const ErrorAlert = (title, text) => {
    $('#question').html('<div class="alert alert-danger alert-dismissible my-auto">' +
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
        '<h5><i class="icon fas fa-exclamation-triangle mr-3"></i>' + title + '</h5><span>' + text + '</span></div>');
    $('#loading').fadeOut();
}

$('#btn_next').click(function () {
    $(this).attr('disabled', true);
    $('#loading').fadeIn(function () {
        SendAnswer(QuestNum, Answer);
    });
});

setTimeout(() => {
    getQuestion(QuestNum);
}, 100);