var isEmailOk = false;

$(document).ready(function () {
    $("#tokenBox").hide();
    $("#password").hide();
    $("#doneImage").hide();
    $("#redirbtn").hide();
    
    $("#button-pass").attr("onclick", "ShowError('Invalid Password')");
    $('#button-email').attr('onclick', "ShowError('Invalid Email')");
    $('#button-token').attr('onclick', "ShowError('Invalid Token')");

    $('#email').keyup(function () {

        checkEmail();

    });

    $('#email').change(function () {

        checkEmail();

    });

    $("#token").keyup(function () {
        checkId();
    });

    $("#token").change(function () {
        checkId();
    });

    $('#pass').keyup(function () {
        checkPassword('p');
    });

    $('#cpass').keyup(function () {
        checkPassword('c');
    });
    


});

function resize()
{
        var wid = $("#contents").width();
        if(wid < 804 && wid > 253)
            {
                var margin  = 805 - wid;
            margin/=2;
            $("#doneImage").css('margin-left',(-margin)+'px');
            $("#doneImage").css('margin-right',(-margin)+'px');
                
            }
    else
        {
            $("#doneImage").css('margin','0');
        }
}



function checkEmail() {
    var value = $("#email").val();
    if (isEmail(value)) {
        $("#g1").removeClass('has-error').addClass('has-success');
        $("#tick").removeClass('glyphicon-remove').addClass('glyphicon-ok')
        isEmailOk = true;
        $("#progress-bar").css('width', '20%');
        $('#button-email').attr('onclick', "buttonClick()");


    } else {
        $("#g1").removeClass('has-success').addClass('has-error');
        $("#tick").removeClass('glyphicon-ok').addClass('glyphicon-remove')
        isEmailOk = false;
        $("#progress-bar").css('width', '10%');
        $('#button-email').attr('onclick', "ShowError('Invalid Email')");


    }
}


function buttonClick() {

    $.ajax({
        url: "reset.php",
        type: "post",
        data: {
            action: 'emailValidate',
            email: $("#email").val(),

        },
        success: function (response) {


            var res = JSON.parse(response);
            if (res.message != "EmailValid") {
                $('#email').effect("shake", {
                    times: 3
                }, 300);
                $("#email-error").html(res.message);
                $("#g1").removeClass('has-success').addClass('has-error');
                $("#tick").removeClass('glyphicon-ok').addClass('glyphicon-remove');

            } else {

                $("#email-validate").fadeOut('medium');
                tempId = res.id;
                $("#progress-bar").css('width', '30%');
                $("#tokenBox").fadeIn('slow');
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }


    });


}

function checkId() {
    var value = $("#token").val();

    if (value.length == 6) {

        $("#g2").removeClass('has-error').addClass('has-success');
        $("#tick-token").removeClass('glyphicon-remove').addClass('glyphicon-ok')
        $("#progress-bar").css('width', '50%');
        $('#button-token').attr('onclick', "buttonClicktoken()");

    } else {
        $("#g2").removeClass('has-success').addClass('has-error');
        $("#tick-token").removeClass('glyphicon-ok').addClass('glyphicon-remove')
        $("#progress-bar").css('width', '40%');
        $('#button-token').attr('onclick', "ShowError('Invalid Token')");

    }
}


var tempId = '';

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}


function buttonClicktoken() {

    $.ajax({
        url: "reset.php",
        type: "post",
        data: {
            action: 'tokenCheck',
            token: $("#token").val(),
            email: $("#email").val(),
            temp: tempId
        },
        success: function (response) {


            var res = JSON.parse(response);

            if (res.message != "tokenValid") {
                $('#token').effect("shake", {
                    times: 3
                }, 300);
                $("#token-error").html(res.message);
                $("#g2").removeClass('has-success').addClass('has-error');
                $("#tick-token").removeClass('glyphicon-ok').addClass('glyphicon-remove');
            } else {

                $("#tokenBox").fadeOut('medium');
                $("#progress-bar").css('width', '60%');
                $("#password").fadeIn('slow');
                tempId = res.token;
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }


    });

}

function checkPassword(select) {
    pass = $("#pass").val();
    cpass = $("#cpass").val();

    console.log(pass);
    console.log(cpass);

    switch (select) {
        case 'c':
            if (pass != cpass) {
                $("#g4").removeClass('has-success').addClass('has-error');
                $("#tick-cpass").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                $("#button-pass").attr("onclick", "ShowError('Invalid Password')");
                $("#progress-bar").css('width', '80%');
                $("#pass-error").html('Both Password Must Match');

            } else {
                $("#g4").removeClass('has-error').addClass('has-success');
                $("#tick-cpass").removeClass('glyphicon-remove').addClass('glyphicon-ok')
                $("#button-pass").attr("onclick", "submitPassword()");
                $("#progress-bar").css('width', '90%');
                $("#pass-error").html('');
            }


            break;
        case 'p':
            switch (checkStrength(pass)) {
                case 'Too short':
                case 'Weak':
                    $("#g3").removeClass('has-success').addClass('has-error');
                    $("#tick-pass").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                    $("#button-pass").attr("onclick", "ShowError('Invalid Password')");
                    $("#progress-bar").css('width', '70%');
                    $("#pass-error").html('Password Is ' + checkStrength(pass));
                    break;
                case 'Good':
                case 'Strong':
                    $("#g3").removeClass('has-error').addClass('has-success');
                    $("#tick-pass").removeClass('glyphicon-remove').addClass('glyphicon-ok')
                    $("#button-pass").attr("onclick", "submitPassword()");
                    $("#progress-bar").css('width', '75%');
                    $("#pass-error").html('Password Is ' + checkStrength(pass));
                    break;

            }
            break;
    }
}


function submitPassword() {
    $.ajax({
            url: "reset.php",
            type: "post",
            data: {
                action: 'changePassword',
                token: $("#token").val(),
                email: $("#email").val(),
                password: $("#pass").val(),
                temp: tempId
            },
            success: function (response) {


                var res = JSON.parse(response);

                if (res.message != "PasswordSet") {
                    $('#token').effect("shake", {
                        times: 3
                    }, 300);
                    $("#password-error").html(res.message);
                    $("#g2").removeClass('has-success').addClass('has-error');
                    $("#tick-token").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                } else {

                    $("#password").fadeOut('medium');
                    $("#progress-bar").css('width', '100%');
                    $("#doneImage").fadeIn('slow');
                    
                    setTimeout($("#progress-div").fadeOut('slow'), 2000);
                        
                    $('#contents').css('margin','0%');
                    $("#contents").css('margin-top','3%');
                    $("#redirbtn").fadeIn('fast');
                    
                    
                    
                }



            

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }


    });
}

function ShowError(message) {
    switch (message) {
        case 'Invalid Email':
            $("#email-error").html(message);
            break;
        case 'Invalid Token':
            $("#token-error").html(message);
            break;
        default:
            $("#pass-error").html(message);
            break;

    }
}

function redir()
{
    window.location = 'index.php';
}
function fin()
{
                    $("#doneImage").fadeIn('slow');
                    $("#email-validate").fadeOut('medium');
                    $("#progress-div").fadeOut('slow');
                    $('#contents').css('margin','0%');
                    $("#contents").css('margin-top','7%');
}