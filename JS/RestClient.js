/**
 * Created by MatrixOfDeath on 24/11/2015.
 */
function query(httpMethod, request){
    $.ajax({
        url: "http://localhost/rest_cw12/server.php",
        type: httpMethod,
        dataType: "json",
        data    : request,
        success : function(response){
                console.log(response);
        }
    });
}