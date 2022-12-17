
<?php
include('./models/db.php');
include('./controllers/User.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Userimp extends Database implements CRUD
{
    private $fname;
    private $lname;
    private $email;
    private $password;
    private $token;
    private $password_comfirm;
    private $id;


    public function getFirst_name()
    {
        return $this->fname;
    }
    public function setFirst_name($name)
    {
        $this->fname = $name;
    }

    public function getLast_name()
    {
        return $this->lname;
    }
    public function setLast_name($name)
    {
        $this->lname = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword_com()
    {
        return $this->password_comfirm;
    }
    public function setPassword_com($password)
    {
        $this->password_comfirm = $password;
    }
    // token getter and setter
    public function getToken()
    {
        return $this->token;
    }
    public function setToken($token)
    {
        $this->token = $token;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    private function sanitize($data)
    {
        // removes whitespace
        $data = trim($data);
        // removes backslashes to clean up data retrieved from a database or from an HTML form
        $data = stripslashes($data);
        // converts special characters into HTML entities.
        $data = htmlspecialchars($data);
        return $data;
    }

    // method to verify the token and set the user to active to 1
    public function verifyEmail()
    {
        // search for the token in the database
        $sql = "SELECT * FROM users WHERE token = :token";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            // update the user to active
            $sql = "UPDATE users SET isactive = 1 WHERE token = :token";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':token', $this->token);
            if ($stmt->execute()) {
                return true;
            }
        } else {
            return false;
        }
    }
    //create a new user
    public function create()
    {
        $this->fname = $this->sanitize($this->fname);
        $this->lname = $this->sanitize($this->lname);
        $this->email = $this->sanitize($this->email);
        $this->password = trim($this->password);
        $this->password_comfirm = trim($this->password_comfirm);
        if (empty($this->fname) || empty($this->lname) || empty($this->email) || empty($this->password)) {
            echo '<div class="alert alert-warning">
            Please fill all the inputs.
        </div>';
        } elseif ($this->password == $this->password_comfirm) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num > 0) {
                echo '<div class="alert alert-warning">
                Email already taken.
            </div>';
                //condition for email format
            } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="alert alert-warning">
                Invalid email format.</div>';
                //condition for password length
            } elseif (strlen($this->password) < 8) {
                echo '<div class="alert alert-warning">
                Password must be at least 8 characters long.</div>';
            } else {
                // :is placeholder
                $this->token = md5(rand() . time());
                $sql = "INSERT INTO users (fname, lname, email, token,  password) VALUES (:fname, :lname, :email, :token, :password)";
                $stmt = $this->connect()->prepare($sql);
                // pass their value as input, and receives the output value
                // binds a value to the placeholder in the SQL
                $stmt->bindParam(':fname', $this->fname);
                $stmt->bindParam(':lname', $this->lname);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':token', $this->token);
                $stmt->bindParam(':password', $this->password);
                if ($stmt->execute()) {
                    $msg = 'Click on the activation link to verify your email. <br><br>
                        <a href="http://localhost/FIFA/verifytoken.php?token=' . $this->token . '"> Click here to verify email</a>
                    ';
                    require './PHPMailer/src/Exception.php';
                    require './PHPMailer/src/PHPMailer.php';
                    require './PHPMailer/src/SMTP.php';
                    //Import PHPMailer classes into the global namespace

                    //Create an instance; passing `true` enables exceptions
                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
                        $mail->isSMTP(); //Send using SMTP
                        $mail->Host = 'smtp.hostinger.com'; //Set the SMTP server to send through
                        $mail->SMTPAuth = true; //Enable SMTP authentication
                        $mail->Username = ''; //SMTP username
                        $mail->Password = '';
                        // enable TLS encryption                          //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        //Enable implicit TLS encryption
                        // Port 587 the default mail submission port. and will provide the best result
                        // Port 587, coupled with TLS encryption
                        $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        $mail->From = 'contact@virtualprovision.ma';
                        $mail->Sender = 'contact@virtualprovision.ma';
                        //Recipients
                        $mail->setFrom('contact@virtualprovision.ma', 'Fifa 2022');
                        $mail->addAddress($this->email, 'Fifa team');
                        //Add a recipient
                        //$mail->addAddress('ellen@example.com');
                        //Name is optional
                        $mail->addReplyTo('contact@virtualprovision.ma', 'fifa team');
                        //$mail->addCC('cc@example.com');
                        //$mail->addBCC('bcc@example.com');

                        //Attachments
                        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                        //set email format to HTML
                        $mail->isHTML(true); //Set email format to HTML
                        $mail->Subject = "welcome to fifa 2022" . $this->fname;
                        $mail->Body = $this->email . "<br>" . $msg;
                        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                        header('Location: ./login.php?verify=true');
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    header('Location: ./login.php?verify=true');
                }
            }
        } else {
            echo '<div class="alert alert-warning">
            passwords dont match
        </div>';
        }
    }
    // login method
    public function login()
    {
        $this->email = $this->sanitize($this->email);
        $this->password = trim($this->password);
        if (empty($this->email) || empty($this->password)) {
            echo '<div class="alert alert-warning">
            Please fill all the inputs.
        </div>';
        } else {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    if ($row['isactive'] == 1) {
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['fname'] = $row['fname'];
                        $_SESSION['lname'] = $row['lname'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['isadmin'] = $row['isadmin'];
                        $_SESSION['isactive'] = $row['isactive'];
                        $_SESSION['logged'] = true;
                        header('location: ./index.php');
                    } else {
                        echo '<div class="alert alert-warning">
                        Please verify your email
                    </div>';
                    }
                } else {
                    echo '<div class="alert alert-warning">
                    Wrong password
                </div>';
                }
            } else {
                echo '<div class="alert alert-warning">
                Email not found
            </div>';
            }
        }
    }
    //logout method
    public function logout()
    {
        session_destroy();
    }
    //method to take data and send an email to the user to reset his password
    public function resetPassword()
    {
        $this->email = $this->sanitize($this->email);
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->token = md5(rand() . time());
            //insert this token in the temporary table
            $sql = "INSERT INTO resetpassword (email, token) VALUES (:email, :token)";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':token', $this->token);
            if ($stmt->execute()) {
                $msg = 'Click on the link to reset your password. <br><br>
                    <a href="http://localhost/FIFA/newpassword.php?token=' . $this->token . '"> Click here to reset password</a>
                ';
                require './PHPMailer/src/Exception.php';
                require './PHPMailer/src/PHPMailer.php';
                require './PHPMailer/src/SMTP.php';
                //Import PHPMailer classes into the global namespace

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
                    $mail->isSMTP(); //Send using SMTP
                    $mail->Host = 'smtp.hostinger.com'; //Set the SMTP server to send through
                    $mail->SMTPAuth = true; //Enable SMTP authentication
                    $mail->Username = ''; //SMTP username
                    $mail->Password = ''; //SMTP password
                    //Enable implicit TLS encryption
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    // Port 587 the default mail submission port. and will provide the best result
                    // Port 587, coupled with TLS encryption
                    $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                    $mail->From = 'contact@virtualprovision.ma';
                    $mail->Sender = 'contact@virtualprovision.ma';
                    //Recipients
                    $mail->setFrom('contact@virtualprovision.ma', 'Fifa 2022');
                    $mail->addAddress($this->email, 'Fifa team');
                    //Add a recipient
                    //$mail->addAddress('ellen@example.com');
                    //Name is optional
                    $mail->addReplyTo('contact@virtualprovision.ma', 'fifa team');
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    //Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                    //set email format to HTML
                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = "welcome to fifa 2022" . $this->fname;
                    $mail->Body = $this->email . "<br>" . $msg;
                    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                    $mail->send();
                    header('Location: ./index.php?reset=true');
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        } else {
            echo '<div class="alert alert-warning">
            Email not found please try again with a valid email address or register first.
        </div>';
        }
    }

    //method to check if the token is valid
    public function token()
    {
        $this->token = $this->sanitize($this->token);
        $sql = "SELECT * FROM resetpassword WHERE token = :token";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->email = $row['email'];
            return true;
        } else {
            // redirect to the index page
            return false;
        }
    }

    //method to update password
    public function updatePassword()
    {
        // making sure password match and not empty
        if ($this->password == $this->password_comfirm && !empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = :password WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':email', $this->email);
            if ($stmt->execute()) {
                // delete the token from the resetpassword table
                $sql = "DELETE FROM resetpassword WHERE email = :email";
                $stmt = $this->connect()->prepare($sql);
                $stmt->bindParam(':email', $this->email);
                if ($stmt->execute()) {
                    header('Location: ./index.php?password=true');
                } else {
                    echo "sorry something wrong happened";
                }
            } else {
                echo "sorry something wrong happened";
            }
        } else {
            echo '<div class="alert alert-warning">
            Passwords do not match
        </div>';
        }
    }
    public function showUser()
    {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //concatinate in the table and fetch the table
            $this->fname = $row['fname'];
            $this->lname = $row['lname'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            echo '<div class="container p-5 bg-danger fullheight">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <form method="post">
                    <div class="form-group p-4">
                        <label class = "text-light" for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" class="form-control" value="' . $this->fname . '">
                        </div>
                        <div class="form-group p-4">
                        <label class = "text-light" for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname" class="form-control" value="' . $this->lname . '">
                        </div>
                        <div class="form-group p-4">
                        <label class = "text-light" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="' . $this->email . '">
                        </div>
                        <div class="form-group p-4">
                        <label class = "text-light" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" value="' . $this->password . '">
                        </div>
                        <div class="form-group p-4">
                        <label class = "text-light" for="password_comfirm">Confirm Password</label>
                        <input type="password" name="password_comfirm" id="password_comfirm" class="form-control" value="' . $this->password . '">
                        </div>
                        <div class="form-group d-flex justify-content-center">
                        <button type="submit" name="update" value="update" class="btn btn-primary m-4">Update User</button>
                        <button type="submit" name="delete" value="delete" class="btn btn-danger m-4">Delete User</button>
                        </div>
                        </form>
                        </div>
                        </div>
                        </div>';
    }
    public function updateUser()
{
        if(!empty($this->fname) && !empty($this->lname) && !empty($this->email) && !empty($this->password) && !empty($this->password_comfirm))
        {
            $this->fname = $this->sanitize($this->fname);
            $this->lname = $this->sanitize($this->lname);
            $this->password = $this->sanitize($this->password);
            $this->password_comfirm = $this->sanitize($this->password_comfirm);
            $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        //update the user
        $sql = "UPDATE users SET fname = :fname, lname = :lname, email = :email, password = :password WHERE id = :id";
        //hash the password
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':fname', $this->fname);
        $stmt->bindParam(':lname', $this->lname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        if(!empty($this->password) && strlen($this->password) > 8 && $this->password == $this->password_comfirm){
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $this->password);
        if($stmt->execute()){
            echo '<div class="alert alert-success">
            your changes have been saved.';
        }else{
            echo '<div class="alert alert-warning">
            something happened please try again.
        </div>';
        }
        } else{
            echo '<div class="alert alert-warning">
            Passwords do not match or empty';
        }
    }
    else{
        echo '<div class="alert alert-warning">
        please fill all the fields';
    }
}

//method to delete profile
    public function deleteUser()
    {
        //remove the user from the database
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $this->id);
    try{
        if ($stmt->execute()) {
            //call logout method
            $this->logout();
        }
    }catch(PDOException $e){
            echo '<div class="alert alert-warning">
            something happened please try again.
        </div>';
        }
    }
}