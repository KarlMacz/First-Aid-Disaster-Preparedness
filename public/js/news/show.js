$(document).ready(function() {
    var commentsLength = 0;

    $(function() {
        $.ajax({
            url: '/admin/graphs/news/views',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: $('#news-block').data('var-id')
            }
        });

        setInterval(function() {
            $.ajax({
                url: '/news/comments',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    newsID: $('#news-block').data('var-id')
                },
                dataType: 'json',
                success: function(response) {
                    var img = '';

                    if(commentsLength !== response['data'].length) {
                        $('#comments-block').html('');

                        for(var i = 0; i < response['data'].length; i++) {
                            if(response['data'][i]['account_info']['image'] === null) {
                                img = 'fadp_anonymous.png';
                            } else {
                                img = response['data'][i]['account_info']['image'];
                            }

                            $('#comments-block').append('<div class="comment block"><div class="image"><img src="/uploads/' + img + '" class="round"></div><div class="content"><div>' + response['data'][i]['comment'] + '</div><div>' + moment(response['data'][i]['created_at']).fromNow() + '</div></div></div>');
                        }

                        commentsLength = response['data'].length;
                    }
                }
            });

            return false;
        }, 1000);
    });

    $('#captcha-modal').on('hidden.bs.modal', function () {
        grecaptcha.reset();
    });

    $('#comment-form').submit(function() {
        $('#captcha-modal').modal('show');

        return false;
    });

    $('#captcha-form').submit(function() {
        $.ajax({
            url: '/captcha/comment',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                grecaptcha.reset();

                $('#captcha-modal').modal('hide');

                if(response['status'] === 'Success') {
                    $.ajax({
                        url: '/news/comment',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: $('#comment-form').serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if(response['status'] === 'Success') {
                                $('#comment-form input[name="comment"]').val('').focus();
                            }
                        }
                    });

                    return false;
                }
            }
        });

        return false;
    });
});
