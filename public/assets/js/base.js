$(document).ready(function(){

    $("#comment-btn").click(function(){

        var commentMsg = $("#new-comment").val();
        var userId = $("#user-comment-id").val();
        var postId = $("#post-comment-id").val();


        if (commentMsg != ''){
            $.ajax({
                type:'POST',
                url:'/comment/post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "commentMsg": commentMsg,
                    "userId": userId,
                    "postId": postId,
                },
                success:function(data) {
                    $("#new-comment").val('');
                    $("#leave-new-comments").append(" <div id=\"comment-" + data.commentId + "\">\n" +
                        "                    <hr>\n" +
                        "                    <div class=\"p-2 bg-white rounded\">\n" +
                        "                        <div class=\"ml-2 row\">\n" +
                        "                            <div class=\"col-11\">\n" +
                        "                                <p class=\"mt-3\">" + data.commentMsg + "</p>\n" +
                        "                                <p class=\"font-weight-bold\">Author: "+ data.userName +"</p>\n" +
                        "                            </div>\n" +
                        "                            <div class=\"col-1\">\n" +
                        "                                    <a onclick=\"deleteComment(" + data.commentId + ")\" style=\"font-size: larger;\" class=\"text-danger\">\n" +
                        "                                        <i class=\"fas fa-trash mt-5\"></i>\n" +
                        "                                    </a>\n" +
                        "                            </div>\n" +
                        "                        </div>\n" +
                        "                    </div>\n" +
                        "                </div>");
                },
                error: function(xhr, status, error) {
                    location.href = '/login';
                }
            });
        }

    });

});

function deleteComment(commentId){
    $.ajax({
        type:'POST',
        url:'/comment/delete',
        data:{
            "_token": "{{ csrf_token() }}",
            "comment_id": commentId,
        },
        success:function(data) {
            $( "#comment-" + commentId).remove();
        },
        error: function(xhr, status, error) {
            console.log(error)
        }
    });
}

function likeClicked(postId){
    $.ajax({
        type:'POST',
        url:'/like/post',
        data: {
            "_token": "{{ csrf_token() }}",
            "post_id": postId,
        },
        success:function(data) {
            if (data.user_likes == true){
                $("#like-" + data.postId).removeClass("btn btn-outline-secondary");
                $("#like-" + data.postId).addClass("btn btn-primary");
                $("#like-" + data.postId).empty();
                $("#like-" + data.postId).append("<i class=\"fas fa-thumbs-up\"></i> " + data.postLikes);
            } else {
                $("#like-" + data.postId).removeClass("btn btn-primary");
                $("#like-" + data.postId).addClass("btn btn-outline-secondary");
                $("#like-" + data.postId).empty();
                $("#like-" + data.postId).append("<i class=\"fas fa-thumbs-up\"></i> " + data.postLikes);
            }
        },
        error: function(xhr, status, error) {
            console.log(error)
        }
    });
}



