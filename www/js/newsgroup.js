$( document ).ready(function () {

    if ($('#groupname').length == 0) {
        return;
    }
    var viewing_id = -1;

    /* Expanding and collapsing posts in the list */
    $( '.expander' ).click(expanderClick);
    $( '.expander' ).dblclick(expanderClick);

    function expanderClick(e) {
        var post_id = $(this).parents('.post').children('.postid').attr('value');
        var post = postui.getPostObjectFromId(post_id);
        if (post.isExpandable()) {
            if (post.isExpanded()) {
                post.collapse();
            } else {
                post.expand();
            }
            /* don't trigger the post view click event */
            e.stopPropagation();
        }
    }

    /* Clicking posts in the list */
    $( '.post' ).click(postItemClick);

    function postItemClick() {
        var post_id = $(this).children('.postid').attr('value');
        var post = postui.getPostObjectFromId(post_id);

        postui.unhighlightAllPosts();
        post.highlight();

        post.setRead();

        showPost(post_id);
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
                checkForNewPosts(true);
            }
        }, 1000);
        if (window.focus) {
            w.focus();
        }
    });

    /* Clicking 'Mark Unread' */
    $( '.markunreadbutton' ).click(function () {
        markUnread(viewing_id);
    });

    /* Clicking 'Delete' */
    $( '.deletebutton' ).click(function () {
        if (window.confirm("Are you sure you want to delete this post and all of its replies?")) {
            deletePost(viewing_id);
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
                checkForNewPosts(true);
            }
        }, 1000);
        if (window.focus) {
            w.focus();
        }
    });

    $( '.refreshbutton' ).click( function() {
        checkForNewPosts(false);
    });

    /* Auto updates */
    var last_update_time = $('#currenttime').attr('value');
    setInterval("checkForNewPosts(true)", 30000);


    function checkForNewPosts(silent) {
        getNewPosts(function (posts) {
            if (posts.length === 0) {
                if (!silent) {
                    alert('No new posts.');
                }
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
                /* Sort them by ascending post time, so newer ones get added
                 * first, and the older ones get added below. */
                posts.sort(function (a,b) {
                    return a.time - b.time;
                });
                for (var i = 0; i < posts.length; i++) {
                    post = posts[i];
                    if (post.parent_id === "") {
                        /* It's a top-level post. */
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
                        // TODO: Make this use the new post reply thing.
                        /* It's a reply. Find the post it's in reply to */
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
                '<colgroup>' + 
                    '<col style="width: 20px;">' + 
                    '<col style="width: auto;">' + 
                    '<col style="width: 150px;">' + 
                    '<col style="width: 190px;">' + 
                '</colgroup>' + 
                '<tr>' +
                    '<td class="expander-dummy">' +
                        '&nbsp;' +
                    '</td>' +
                    '<td class="titlecell newunread">' +               // indent
                        '<span class="posttitle">' +
                            'XXX' +                                 // title
                        '</span>' +
                    '</td>' +
                    '<td class="metadatauser newunread">' +
                        'XXX' +                                     // user
                    '</td>' + 
                    '<td class="metadatatime newunread">' +
                        'XXX' +                                     // time
                    '</td>' +
                '</tr>' + 
            '</table>' +
        '</div>';
        var obj = $('<div></div>').html(html).children();
        /* data */
        obj.find('.postid').val(post.id);
        obj.find('.postindent').val(indent);
        obj.find('.posttitle').css('paddingLeft', 10 + 30*indent);
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

    function markUnread(post_id) {
        var data = { };
        data.mark_unread_id = post_id;
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                var post = postui.getPostObjectFromId(post_id);
                post.setUnread();
            } else {
                alert('Error marking post as unread.');
            }
        });
    }

    function deletePost(post_id) {
        var data = {};
        data.delete_post_id = post_id;
        $.post("ajax.php", data, function (data) {
            var stat = $(data).find('status').text();
            if (stat === 'success') {
                $("#postview").hide();
                var post = postui.getPostObjectFromId(post_id);
                post.remove();
            } else {
                alert('The post could not be deleted. Either it is already gone or someone else replied to it and you are not an administrator');
            }
        });
    }

    function getPostFromXML(xml) {
        var post = {};
        post.id = $(xml).find('id').text();
        post.parent_id = $(xml).find('parent').text();
        post.user = $(xml).find('user').text();
        post.time = parseInt($(xml).find('time').text(), 10);
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
                $(".vp_date").text(post.formatted_time);
                $("#postcontents").html(post.contents);
                $("#postview").show();
            } else {
                alert('That post has been deleted.');
            }
        }, true);
    }


});
