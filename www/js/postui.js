
var postui;
if (!postui) var postui = {};

postui.unhighlightAllPosts = function() {
    /* un-highlight (de-select) all the other posts */
    $('.post').css('background-color', 'inherit');
};

/* Given a post id, returns an object with methods that make it easier to update
 * the UI after operations. If the current post list does not contain a post
 * with the same id, this function returns false. */
postui.getPostObjectFromId = function(post_id) {
    var post = {};

    post.setRead = function() {
        /* Set this post's read status. If it's a top-level parent with unread
         * replies, we have to change it to 'subunread' instead of just 'read'.
         * */
        if (this.isTopLevelPost() && this.isChildPostUnread()) {
            this.getPostDiv().find('.unread').removeClass('unread').addClass('subunread');
            this.getPostDiv().find('.newunread').removeClass('newunread').addClass('subunread');
        } else {
            /* Otherwise, it's a reply or there are no unread replies, and so we
             * can set it to 'read'. */
            this.getPostDiv().find('.unread').removeClass('unread').addClass('read');
            this.getPostDiv().find('.newunread').removeClass('newunread').addClass('read');
        }

        /* If this post was the last unread reply to its top-level parent, we
         * may have to set the top-level parent's status to 'read'. */
        if (!this.isTopLevelPost()) {
            this.getTopLevelParent().fixReadStatus();
        }
    };

    post.setUnread = function () {
        this.getPostDiv().find('.read').removeClass('read').addClass('unread');
        this.getPostDiv().find('.subread').removeClass('read').addClass('unread');
        if (!this.isTopLevelPost()) {
            this.getTopLevelParent().fixReadStatus();
        }
    };

    post.remove = function () {
        /* Get the top-level parent BEFORE removing, since after removal, we
         * won't be able to find the top-level parent. */
        var is_top_level = this.isTopLevelPost();
        var top_level_parent = this.getTopLevelParent();

        /* Remove all replies to this post. */
        this.getPostDiv().next().remove();
        /* Remove this post. */
        this.getPostDiv().remove();

        /* -- After this point, we shouldn't expect any functions in this object to work. -- */

        if (!is_top_level) {
            top_level_parent.fixReadStatus();
        }
    };

    /* Call this function on the top-level parent after the read-status of any
     * of its children have changed to correct its read-status. */
    post.fixReadStatus = function() {
        if (this.isTopLevelPost()) {
            if (this.isChildPostUnread()) {
                /* If there are unread children, but we're 'read', change to * 'subunread'. */
                this.getPostDiv().find('.read').removeClass('read').addClass('subunread');
            } else {
                /* If we're 'subunread', but there are NO unread children, * change to 'read.' */
                this.getPostDiv().find('.subunread').removeClass('subunread').addClass('read');
            }
        } else {
            alert('This function should not be called on a reply post.');
        }
    };

    post.expand = function () {
        var post_div = this.getPostDiv();
        var expander = post_div.find('.expander');
        post_div.next().show();
        expander.html('&ndash;');
    };

    post.collapse = function () {
        var post_div = this.getPostDiv();
        var expander = post_div.find('.expander');
        post_div.next().hide();
        expander.html('+');
    };

    post.isExpanded = function () {
        var expander = this.getPostDiv().find('.expander');
        return  $.trim(expander.text()) !== '+';
    };

    post.isExpandable = function () {
        var expander = this.getPostDiv().find('.expander');
        return  $.trim(expander.text()) !== '';
    };

    post.highlight = function () {
        this.getPostDiv().css('background-color', '#00FFFF');
    };

    post.isTopLevelPost = function () {
        /* We're a top-level post if we're NOT in a .hiddenposts div. */
        var reply_container = this.getPostDiv().parents('.hiddenposts');
        return reply_container.length === 0;
    };

    post.getTopLevelParent = function () {
        if (this.isTopLevelPost()) {
            return this;
        } else {
            var reply_container = this.getPostDiv().parents('.hiddenposts');
            return getPostObjectFromId(
                reply_container.prev('.post').children('.postid').attr('value')
            );
        }
    };

    post.isChildPostUnread = function () {
        var reply_container = this.getPostDiv().next();
        return reply_container.find('.unread, .newunread').length > 0;
    };

    post.getPostDiv = function () {
        var p = $('.postid').filter("[value='" + post_id + "']");
        return p.parents('.post');
    };

    if (post.getPostDiv().length == 0) {
        return false;
    }

    return post;
};


