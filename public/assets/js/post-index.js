$(document).ready(function () {
    $('#filter-arrow-up').hide();
    $('#filters-row').hide();
});

function showFilters() {
    $('#filter-arrow-down').hide();
    $('#filter-arrow-up').show();
    $('#filters-row').fadeIn();
}

function hideFilters() {
    $('#filter-arrow-down').show();
    $('#filter-arrow-up').hide();
    $('#filters-row').fadeOut();
}

$('#post-name').keyup( function (){

    const postName = this.value;
    userFilterCall(postName);

});

function userFilterCall(postName){

    $.ajax({
        type:'POST',
        url:'/post/filter/post',
        data: {
            "_token": "{{ csrf_token() }}",
            "post_name": postName,
        },
        success:function(data) {
            const posts = data.posts;
            $("#user-body-table").find("tr:gt(0)").remove();
            $("#pagination-holder").remove();

            posts.forEach(function (post){

                $("#user-body-table").append('                ' +
                    '<tr>\n' +
                    '                    <td>'+ post.id +'</td>\n' +
                    '                    <td>'+ post.name +'</td>\n' +
                    '                    <td>'+ post.body.substring(0, 30) +'</td>\n' +
                    '                    <td><img src="'+ uploadsBaseDir +post.image+'" width="100" height="50"></td>\n' +
                    '                    <td><a class="btn btn-success" href="/post/'+post.id+'">show</a></td>\n' +
                    '                    <td><a class="btn btn-warning" href="/post/'+post.id+'/edit">edit</a></td>\n' +
                    '                    <td></td>\n' +
                    '                </tr>');
            });
            $("#user-body-table").find("tr:gt(0)").hide();
            $("#user-body-table").find("tr:gt(0)").show('slow');
        },
        error: function(xhr, status, error) {
            console.log(error);
        }
    });

}