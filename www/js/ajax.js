
var ajax;
if (!ajax) var ajax = {};

ajax.getPost = function(id, f, mark_read) {
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
};

ajax.getNewPosts = function(f) {
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
};

ajax.markUnread = function (post_id) {
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
};

ajax.deletePost = function(post_id) {
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
};

ajax.getPostFromXML = function(xml) {
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
