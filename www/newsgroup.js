$( document ).ready(function () {

    var viewing_id = -1;

    /* Expanding and collapsing posts */
    $( '.expander' ).click(function (e) {
        if ($.trim($(this).text()) === '+') {
            $(this).parents('.post').next().show(100);
            $(this).html('&ndash;');
        } else {
            $(this).parents('.post').next().hide(100);
            $(this).html('+');
        }
        /* don't trigger the post view click event */
        e.stopPropagation();
    });

    /* Clicking posts in the list */
    $( '.post' ).click(function () {
        var id = $(this).children('.postid').attr('value');
        $('.post').css('background-color', 'inherit');
        $(this).css('background-color', 'cyan');
        showPost(id);
    });

    /* Double clicking posts in the list */
    $( '.post' ).dblclick(function(e) {
        var id = $(this).children('.postid').attr('value');
        var w = window.open(
            'viewpost.php?id=' + id,
            "vp_" + id,
            'width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'
        );
        if (window.focus) {
            w.focus();
        }
    });

    /* Clicking reply */
    $( '.replybutton' ).click(function () {
        var w = window.open(
            'newpost.php?replyto=' + viewing_id,
            'ep_' + viewing_id,
            'width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0'
        );
        if (window.focus) {
            w.focus();
        }
    });

    function getPost(id, f) {
        var data = {
            id: id
        };
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                var post = {};
                post.id = $(data).find('id').text();
                post.user = $(data).find('user').text();
                post.time = $(data).find('time').text();
                post.title = $(data).find('title').text();
                post.contents = $(data).find('contents').html();
                f(post);
            } else {
                f(null);
            }
        });
    }

    function showPost(id) {
        getPost(id, function (post) {
            if (post !== null) {
                viewing_id = id;
                $(".vp_user").text(post.user);
                $(".vp_date").text(post.time);
                $("#postcontents").html(post.contents);
                $("#postview").show("fast");
            } else {
                // TODO
                alert(1);
            }
        });
    }

});
