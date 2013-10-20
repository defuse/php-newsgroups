/*
 * Functions for making AJAX calls to the server.
 */

var ajax;
if (!ajax) var ajax = {};

ajax.last_update_time = 0;

ajax.getPost = function(id, f, mark_read) {
    var data = { };
    data.id = id;
    data.mark_read = mark_read ? "1" : "0";
    $.post("ajax.php", data, function (data) {
        var stat = $(data).find('status').text();
        if (stat === 'success') {
            f(ajax.getPostFromXML(data));
        } else {
            f(null);
        }
    }, "xml");
};

ajax.getNewPosts = function(group_name, f) {
    var data = { };
    data.get_posts_after = ajax.last_update_time;
    data.newsgroup = group_name;
    $.post("ajax.php", data, function (data) {
        var stat = $(data).find('status').text();
        if (stat === 'success') {
            ajax.last_update_time = $(data).find('currenttime').text();
            var posts_xml = $(data).find('post')
            var posts = [];
            for (var i = 0; i < posts_xml.length; i++) {
                posts.push(ajax.getPostFromXML(posts_xml[i]));
            }
            f(posts);
        } else {
            f(null);
        }
    }, "xml"); 
};

ajax.markUnread = function (post_id, f) {
    var data = { };
    data.mark_unread_id = post_id;
    $.post("ajax.php", data, function (data) {
        var stat = $(data).find('status').text();
        f(stat === 'success');
    });
};

ajax.deletePost = function(post_id, f) {
    var data = {};
    data.delete_post_id = post_id;
    $.post("ajax.php", data, function (data) {
        var stat = $(data).find('status').text();
        f(stat === 'success');
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
