<?php
session_start();
if($_SESSION['username'] != "none") {
    header("Location: ../index.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../controller/config.php";
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT id, username, password FROM usuarios WHERE username = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        
        $param_username = $username;
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1){                    
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if(mysqli_stmt_fetch($stmt)){
                    if($password == $hashed_password){
                        session_start();
                        
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;                            
                        
                        header("location: ../index.php");
                    } else{
                        header("Location: ../view/login.php?error=2");
                        exit;
                    }
                }
            } else{
                header("Location: ../view/login.php?error=1");
                exit;
            }
        } else{
            echo "Oops! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>
