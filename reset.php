<?php
session_start();
include 'functions.php';
include './phpIncludes/PHPMailer/PHPMailerAutoload.php';



if(isset($_POST['action']))
{
    $email = '';
    $action = $_POST['action'];
    $id = '';
    $temp = '';
    switch ($action)
    {
        case 'emailValidate':
            if(isset($_POST['email']))
            {
                $email = strtolower($_POST['email']);
                
                if(isEmailValid($email))
                {
                    
                    if(entryExists('Login','Email',$email))
                    {
                        $_SESSION['email'] = $email;
                        
                        $id = generateId(6,'password');
                        $_SESSION['id'] = $id;
                        
                        
                        
                        updateRecord('Login','Token',$id,'Email',$email);
                        updateRecord('Login','Tries',0,'Email',$email);

                        
                        $temp = generateId(6,'password');
                        $_SESSION['temp'] = $temp;
                        
                        
                        echo "{
                            \"id\":\"$temp\",
                            \"message\":\"EmailValid\"
                        }";
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://tabe.epizy.com/server.php?email=$email&id=$id");

                        
                        
                        
                    }
                    else{
                        echo "{\"message\":\"Email Not Found On Server\"}";
                    }
                }
                else
                {
                    echo "{\"message\":\"Email Not Valid\"}";  
                }
            }
            else
            {
                echo "{\"message\":\"No Email Given\"}";
            }
            break;
            
            
        case 'tokenCheck':
            if(isset($_POST['temp']))
            {
                    //echo  $_POST['id'];
                    //echo  $_SESSION['temp'];
                
                if($_POST['temp'] == $_SESSION['temp'])
                {
                    
                    if($_SESSION['email'] == $_POST['email'])
                    {
                        $email = $_SESSION['email'];
                        $query = "select Tries from Login where email='$email'";
                        $result = runSelectQuery($query);
                        $tries = $result->Tries;
                        
                        //echo $_POST['id'];
                        
                        if($tries>3)
                        {
                            echo "{\"message\":\"Too many Attempts Please try after some time\"}";
                        }
                        else
                        {
                            if(isset($_POST['token']))
                            {
                                
                                $query = "select Token from Login where email='$email'";
                                $result = runSelectQuery($query);
                                $token = $result->Token;
                                updateRecord('Login','Tries',$tries+1,'Email',$email);
                                
                                //echo $token;
                                
                                
                                if($_POST['token'] == $token)
                                {   
                                    $temp = generateId(6,'password');
                                    $_SESSION['temp'] = $temp;
                                 
                                    updateRecord('Login','Tries',0,'Email',$email);
                                    echo "{\"message\":\"tokenValid\",\"token\":\"$temp\"}";
                                    $_SESSION['toknValid']='valid';
                                    
                                    
                                    
                                    
                                    
                                    
                                }
                                else
                                {
                                    echo "{\"message\":\"Token Miss-Match\"}";
                                }

                            }
                            else
                            {
                                echo "{\"message\":\"No token Recived\"}";
                            }
                            
                        }
                        
                        
                    }
                    else
                    {
                        echo "{\"message\":\"Email Address Changed please retry\"}";
                    }
                }
                else{
                    echo "{\"message\":\"Session Variable Changed Please Retry\"}";
                }
            }
            else
            {
                echo "{\"message\":\"No session variable found\"}";
            }
            break;
        case 'changePassword':
            if(isset($_SESSION['toknValid']))
            {
                 if(isset($_POST['temp']))
            {
                    //echo  $_POST['id'];
                    //echo  $_SESSION['temp'];
                
                if($_POST['temp'] == $_SESSION['temp'])
                {
                    
                    if($_SESSION['email'] == $_POST['email'])
                    {
                        $email = $_SESSION['email'];
                       
                            if(isset($_POST['password']))
                            {
                                $pass = $_POST['password'];
                                if(strlen($pass)>6)
                                {
                                    updateRecord('Login','Password',$pass,'Email',$email);
                                    echo "{\"message\":\"PasswordSet\"}"; 
                                }
                                else
                                {
                                    "{\"message\":\"Password must be atleast 7 Charachters Long\"}"; 
                                }
                            }
                            else
                        {
                           echo "{\"message\":\"No Password Found\"}"; 
                        }
                        
                        
                        
                    }
                    else
                    {
                        echo "{\"message\":\"Email Address Changed please retry\"}";
                    }
                }
                else{
                    echo "{\"message\":\"Session Variable Changed Please Retry\"}";
                }
            }
            else
            {
                echo "{\"message\":\"No session variable found\"}";
            }
            }
            else
            {
                echo "{\"message\":\"Token is Not Verified\"}";
            }
            break;
    }
}
else
{
    require('reset.html');
    die();
}

function isEmailValid($email)
{
     return (filter_var($email, FILTER_VALIDATE_EMAIL));
}

function check($data,$length = 2)
{
    return (strlen($data)>$length);
}
function  sendmail($email,$id)
{
    
$mail = new PHPMailer();
$mail->IsSMTP(); // send via SMTP
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->Username = "YOUR_Email_address"; // Enter your SMTP username
$mail->Password = "YOUR_Password"; // SMTP password

$webmaster_email = "no-replay@tabe.com"; //Add reply-to email address

$mail->From = $webmaster_email;
$mail->FromName = "Tabe Admin";

$mail->AddAddress($email,"Nominator");
$mail->AddReplyTo($webmaster_email,"Tabe Admin");

$mail->Subject = "Password Reset";

$mail->Body ="MAIL BODY";

$mail->send();
echo mail('TO_MAIL_ID',$Subject,$Body);    
    
    
}
                        
                        
                        
