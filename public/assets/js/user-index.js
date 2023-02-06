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

$('#user-name').keyup( function (){

    const userName = this.value;
    const userEmail= $('#user-email').val();

    userFilterCall(userName, userEmail);

});

$('#user-email').keyup( function (){

    const userEmail = this.value;
    const userName= $('#user-name').val();

    userFilterCall(userName, userEmail);

});


function userFilterCall(userName, userEmail){

    $.ajax({
        type:'POST',
        url:'/filter/user',
        data: {
            "_token": "{{ csrf_token() }}",
            "user_name": userName,
            "user_email": userEmail,
        },
        success:function(data) {
            const users = data.users;
            $("#user-body-table").find("tr:gt(0)").remove();
            $("#pagination-holder").remove();

            users.forEach(function (user){

                let role = '';

                if(user.roles.includes("ROLE_ADMIN")){
                   role = '<div class="btn btn-success">Administrator</div>'
                } else {
                   role = '<div class="btn btn-warning">User</div>'
                }

                $("#user-body-table").append('                ' +
                    '<tr>\n' +
                    '                    <td>'+ user.id +'</td>\n' +
                    '                    <td>'+ user.name +'</td>\n' +
                    '                    <td>'+ user.email +'</td>\n' +
                    '                    <td>'+role+'</td>\n' +
                    '                    <td><img width="50" height="50" src="https://lh3.googleusercontent.com/proxy/wNtIseYCLB7KYxE4hZ_4mO9gDrN0Wt7p8JUmepcm5BnGipo54-nwf312wDffc5kmaJwLB-qe2yG7Ua5SMuj2wufwnlxkfhKzQUO3WGzS41zlnJrqdyLNaFroteB0vfIRg3K0H5VnpEfG2nIPxEyLWnlWXf91VsAwG2L6"></td>\n' +
                    '                    <td class="font-weight-bold"><span style="color: #3c4caa">M</span>/<span style="color: palevioletred">F</span></td>\n' +
                    '                    <td></td>\n' +
                    '                </tr>');
            });
            $("#user-body-table").find("tr:gt(0)").hide();
            $("#user-body-table").find("tr:gt(0)").show('slow');
        },
        error: function(xhr, status, error) {
            console.log("Error");
        }
    });

}
