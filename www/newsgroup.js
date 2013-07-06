$( document ).ready(function () {

    var viewing_id = -1;

    /* Expanding and collapsing posts in the list */
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
        /* get the id of the post that was just clicked */
        var id = $(this).children('.postid').attr('value');
        /* un-highlight all the other posts */
        $('.post').css('background-color', 'inherit');
        /* highlight the one that was just clicked */
        $(this).css('background-color', '#00FFFF');

        /* if this is a top-level post, and there are unread sub-posts... */
        if ($(this).next('.hiddenposts').find('.unread').length > 0) {
            $(this).find('.unread').removeClass('unread').addClass('subunread');
        } else {
            $(this).find('.unread').removeClass('unread').addClass('read');
        }

        var reply_container = $(this).parents('.hiddenposts');
        /* if this is a reply post */
        if (reply_container.length > 0) {
            root_post = reply_container.prev('.post');
            /* if all other replies to the 'root' post are read */
            if (reply_container.find('.unread').length === 0) {
                root_post.find('.subunread').removeClass('subunread').addClass('read');
            }
        }
        showPost(id);
    });

    /* Double clicking posts in the list */
    $( '.post' ).dblclick(function(e) {
        alert("Normally this would open the post in a new window, but isn't implemented.");
        return;
        var id = $(this).children('.postid').attr('value');
        var w = window.open(
            'viewpost.php?id=' + id,
            '_blank'
        );
        if (window.focus) {
            w.focus();
        }
    });

    /* Clicking 'Reply' */
    $( '.replybutton' ).click(function () {
        var w = window.open(
            'replypost.php?replyto=' + viewing_id,
            '_blank'
        );
        if (window.focus) {
            w.focus();
        }
    });

    /* Clicking 'New Post' */
    $( '.newpostbutton' ).click(function () {
        var w = window.open(
            'newpost.php?group=' + groupName(),
            '_blank'
        );
        if (window.focus) {
            w.focus();
        }
    });

    function groupName() {
        return $('#groupname').attr('value');
    }

    function getPost(id, f, mark_read) {
        var data = { }
        data.id = id;
        data.mark_read = mark_read ? "1" : "0";
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                var post = {};
                post.id = $(data).find('id').text();
                post.user = $(data).find('user').text();
                post.time = $(data).find('time').text();
                post.title = $(data).find('title').text();
                post.contents = $(data).find('contents').text();
                f(post);
            } else {
                f(null);
            }
        }, "xml");
    }

    function showPost(id) {
        getPost(id, function (post) {
            if (post !== null) {
                viewing_id = id;
                if (post.user === "") {
                    $(".vp_user").html('<i>Anonymous</i>');
                } else  {
                    $(".vp_user").text(post.user);
                }
                $(".vp_subject").text(post.title);
                $(".vp_date").text(post.time);
                $("#postcontents").html(post.contents);
                $("#postview").show("fast");
            } else {
                alert('That post has been deleted.');
            }
        }, true);
    }

});
