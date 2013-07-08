$( document ).ready(function () {

    var viewing_id = -1;

    /* FIXME: this stuff shouldn't run if no newsgroup is displayed */

    /* Expanding and collapsing posts in the list */
    $( '.expander' ).click(expanderClick);
    $( '.expander' ).dblclick(expanderClick);

    function expanderClick(e) {
        if ($.trim($(this).text()) === '+') {
            $(this).parents('.post').next().show();
            $(this).html('&ndash;');
        } else {
            $(this).parents('.post').next().hide();
            $(this).html('+');
        }
        /* don't trigger the post view click event */
        e.stopPropagation();
    }

    /* Clicking posts in the list */
    $( '.post' ).click(postItemClick);

    function postItemClick() {
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
    }

    /* Double clicking posts in the list */
    $( '.post' ).dblclick(postItemDoubleClick);

    function postItemDoubleClick() {
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
    }

    /* Clicking 'Reply' */
    $( '.replybutton' ).click(function () {
        var w = window.open(
            'replypost.php?replyto=' + viewing_id,
            '_blank'
        );
        var pollTimer = setInterval(function () {
            if (w.closed !== false) {
                window.clearInterval(pollTimer);
                checkForNewPosts();
            }
        }, 1000);
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
        var pollTimer = setInterval(function () {
            if (w.closed !== false) {
                window.clearInterval(pollTimer);
                checkForNewPosts();
            }
        }, 1000);
        if (window.focus) {
            w.focus();
        }
    });

    /* Auto updates */
    var last_update_time = $('#currenttime').attr('value');
    setInterval(checkForNewPosts, 30000);

    function checkForNewPosts() {
        getNewPosts(function (posts) {
            if (posts.length === 0) {
                return;
            }
            /* Here we keep iterating over the list, trying to add posts into
            * the DOM. We finish when no change is made. We have to do this
            * because the 'posts' array might contain posts that are replies to
            * each other, and we can't add the reply without first adding the
            * post it is in reply to. */
            var changed = false;
            var unadded_posts;
            var post;
            do {
                changed = false;
                unadded_posts = [];
                for (var i = 0; i < posts.length; i++) {
                    post = posts[i];
                    if (post.parent_id === "") {
                        if (pageNumber() === 1) {
                            changed = true;
                            $('#postlisting').prepend('<div class="hiddenposts"></div>');
                            $('#postlisting').prepend(createUnreadPost(post, 0));
                        } else {
                            /* We don't add it to unadded_posts when page != 1,
                            * since it's not in reply to anything, so we won't be
                            * able to add it later. */
                        }
                    } else {
                        /* find the post it's in reply to */
                        var p = $('.postid').filter("[value='" + post.parent_id + "']");
                        if (p.length > 0) {
                            changed = true;
                            p = p.parents('.post');
                            var indent = parseInt(p.find('.postindent').val());
                            var post_obj = createUnreadPost(post, indent + 1);
                            var toplevel;
                            if (indent === 0) {
                                p.next('.hiddenposts').append(post_obj);
                                p.next('.hiddenposts').append('<div class="childposts"></div>');
                                toplevel = p;
                            } else {
                                p.next('.childposts').append(post_obj);
                                p.next('.childposts').append('<div class="childposts"></div>');
                                toplevel = p.parents('.hiddenposts').prev('.post');
                            }
                            toplevel.find('.expander-dummy')
                                    .removeClass('expander-dummy')
                                    .addClass('expander')
                                    .text('+')
                                    .click(expanderClick)
                                    .dblclick(expanderClick);
                            toplevel.find('.read')
                                    .removeClass('read')
                                    .addClass('subunread');
                        } else {
                            /* might not have added the parent yet, try again */
                            unadded_posts.push(post);
                        }
                    }
                }
                posts = unadded_posts;
            } while (changed);
        });
    }

    function pageNumber() {
        return parseInt($('#grouppagenumber').attr('value'));
    }

    function createUnreadPost(post, indent) {
        var html = '<div class="post">' +
            '<input type="hidden" class="postid" value="XXX"/>' +   // id
            '<input type="hidden" class="postindent" value="XXX"/>' + // indent
            '<table class="posttable" cellspacing="0">' +
                '<tr>' +
                    '<td class="expander-dummy">' +
                        '&nbsp;' +
                    '</td>' +
                    '<td class="titlecell unread">' +               // indent
                        '<span class="posttitle">' +
                            'XXX' +                                 // title
                        '</span>' +
                    '</td>' +
                    '<td class="metadatacell unread">' +
                        '<table class="metadatatable" cellspacing="0" cellpadding="0">' +
                            '<tr>' +
                                '<td></td>' +
                                '<td class="metadatauser">' +
                                    'XXX' +                         // user
                                '</td>' +
                                '<td class="metadatatime">' +
                                    'XXX' +                         // time
                                '</td>' + 
                            '</tr>' +
                        '</table>' +
                    '</td>' +
                '</tr>' + 
            '</table>' +
        '</div>';
        var obj = $('<div></div>').html(html).children();
        /* data */
        obj.find('.postid').val(post.id);
        obj.find('.postindent').val(indent);
        obj.find('.titlecell').css('paddingLeft', 10 + 30*indent);
        obj.find('.posttitle').text(post.title);
        obj.find('.metadatauser').text(post.user);
        obj.find('.metadatatime').text(post.formatted_time);
        /* events */
        obj.click(postItemClick);
        obj.dblclick(postItemDoubleClick);
        return obj;
    }

    function groupName() {
        return $('#groupname').attr('value');
    }

    function getPost(id, f, mark_read) {
        var data = { };
        data.id = id;
        data.mark_read = mark_read ? "1" : "0";
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                f(getPostFromXML(data));
            } else {
                f(null);
            }
        }, "xml");
    }

    function getNewPosts(f) {
        var data = { };
        data.get_posts_after = last_update_time;
        data.newsgroup = groupName();
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                last_update_time = $(data).find('currenttime').text();
                var posts_xml = $(data).find('post')
                var posts = [];
                for (var i = 0; i < posts_xml.length; i++) {
                    posts.push(getPostFromXML(posts_xml[i]));
                }
                f(posts);
            } else {
                f(null);
            }
        }, "xml"); 
    }

    function getPostFromXML(xml) {
        var post = {};
        post.id = $(xml).find('id').text();
        post.parent_id = $(xml).find('parent').text();
        post.user = $(xml).find('user').text();
        post.time = $(xml).find('time').text();
        post.formatted_time = $(xml).find('formattedtime').text();
        post.title = $(xml).find('title').text();
        post.contents = $(xml).find('contents').text();
        return post;
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
                $("#postview").show();
            } else {
                alert('That post has been deleted.');
            }
        }, true);
    }

});
