/* 
 * Implementation of the newsgroup listing interface.
 */

var ng_ui;
if (!ng_ui) var ng_ui = {};

/* The id of the currently-displayed post. Initially none. */
ng_ui.viewing_id = -1;

$( document ).ready(function () {

    /* Check if we're viewing a newsgroup. If not, there's nothing to do. */
    if ($('#groupname').length == 0) {
        return;
    }

    /* Expanding and collapsing posts in the list. */
    $( '.expander' ).click(ng_ui.expanderClick);
    $( '.expander' ).dblclick(ng_ui.expanderClick);

    /* Clicking posts in the list. */
    $( '.post' ).click(ng_ui.postItemClick);

    /* Double-clicking posts in the list. */
    $( '.post' ).dblclick(ng_ui.postItemDoubleClick);

    /* Clicking 'Reply'. */
    $( '.replybutton' ).click(function () {
        var w = window.open(
            'replypost.php?replyto=' + ng_ui.viewing_id,
            '_blank'
        );
        var pollTimer = setInterval(function () {
            if (w.closed !== false) {
                window.clearInterval(pollTimer);
                ng_ui.checkForNewPosts(true);
            }
        }, 1000);
        if (window.focus) {
            w.focus();
        }
    });

    /* Clicking 'Mark Unread' */
    $( '.markunreadbutton' ).click(function () {
        var id_to_unread = ng_ui.viewing_id;
        ajax.markUnread(id_to_unread, function (success) {
            if (success) {
                var post = postui.getPostObjectFromId(id_to_unread);
                post.setUnread();
            } else { 
                alert('Error marking post as unread.');
            }
        });
    });

    /* Clicking 'Delete' */
    $( '.deletebutton' ).click(function () {
        var id_to_delete = ng_ui.viewing_id;
        if (window.confirm("Are you sure you want to delete this post and all of its replies?")) {
            ajax.deletePost(id_to_delete, function (success) {
                if (success) {
                    var post = postui.getPostObjectFromId(id_to_delete);
                    post.remove();
                    postui.hidePostViewer();
                } else {
                    alert(
                        'The post could not be deleted. Either it is already ' +
                        'gone or someone else replied to it and you are not ' +
                        'an administrator'
                    );
                }
            });
        }
    });

    /* Clicking 'New Post' */
    $( '.newpostbutton' ).click(function () {
        var w = window.open(
            'newpost.php?group=' + ng_ui.groupName(),
            '_blank'
        );
        var pollTimer = setInterval(function () {
            if (w.closed !== false) {
                window.clearInterval(pollTimer);
                ng_ui.checkForNewPosts(true);
            }
        }, 1000);
        if (window.focus) {
            w.focus();
        }
    });

    $( '.refreshbutton' ).click( function() {
        ng_ui.checkForNewCancellations();
        ng_ui.checkForNewPosts(false);
    });

    /* Automatically check for new posts. */
    ajax.last_update_time = $('#currenttime').attr('value');
    setInterval(function () {
        ng_ui.checkForNewPosts(true);
    }, 30000);

    /* Automatically check for deleted posts. */
    ajax.last_cancel_id = $('#last_cancel_id').attr('value');
    setInterval(function () {
        ng_ui.checkForNewCancellations();
    }, 30000);

});

ng_ui.postItemClick = function () {
    var post_id = $(this).children('.postid').attr('value');
    var post = postui.getPostObjectFromId(post_id);
    post.highlight();
    post.setRead();
    ng_ui.showPost(post_id);
};

ng_ui.postItemDoubleClick = function () {
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
};

ng_ui.expanderClick = function(e) {
    var post_id = $(this).parents('.post').children('.postid').attr('value');
    var post = postui.getPostObjectFromId(post_id);
    if (post.isExpandable()) {
        if (post.isExpanded()) {
            post.collapse();
        } else {
            post.expand();
        }
        /* Don't trigger the .post click event. Don't display the post. */
        e.stopPropagation();
    }
};

ng_ui.checkForNewCancellations = function () {
    ajax.getNewCancellations(ng_ui.groupName(), function (cancellations) {
        for (var i = 0; i < cancellations.length; i++) {
            var cancelled_post_id = cancellations[i];
            var post = postui.getPostObjectFromId(cancelled_post_id);
            if (post !== false) {
                post.remove();
            }
            if (cancelled_post_id == ng_ui.viewing_id) {
                postui.hidePostViewer();
            }
        }
    });
};

ng_ui.checkForNewPosts = function(silent) {
    ajax.getNewPosts(ng_ui.groupName(), function (posts) {
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
                    if (ng_ui.pageNumber() === 1) {
                        changed = true;
                        $('#postlisting').prepend('<div class="hiddenposts"></div>');
                        $('#postlisting').prepend(postui.createUnreadPost(post, 0));
                    } else {
                        /* We don't add it to unadded_posts when page != 1,
                        * since it's not in reply to anything, so we won't be
                        * able to add it later. */
                    }
                } else {
                    /* It's a reply. Find the post it's in reply to */
                    var p = postui.getPostObjectFromId(post.parent_id);
                    if (p) {
                        p.addChildPost(post);
                        changed = true;
                    } else {
                        /* might not have added the parent yet, try again */
                        unadded_posts.push(post);
                    }
                }
            }
            posts = unadded_posts;
        } while (changed);
    });
};

ng_ui.pageNumber = function () { 
    return parseInt($('#grouppagenumber').attr('value'));
};

ng_ui.groupName = function () {
    return $('#groupname').attr('value');
};

ng_ui.showPost = function (id) {
    ajax.getPost(id, function (post) {
        if (post !== null) {
            ng_ui.viewing_id = id;
            if (post.user === "") {
                $(".vp_user").html('<i>Anonymous</i>');
            } else  {
                $(".vp_user").text(post.user);
            }
            $(".vp_subject").text(post.title);
            $(".vp_date").text(post.formatted_time);
            $("#postcontents").html(post.contents);
            postui.showPostViewer();
        } else {
            alert('That post has been deleted.');
        }
    }, true);
};

