
<?php
include('./models/db.php');
include('./controllers/User.php');
include('./config/passwords.php');
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

    private function sanitize($data)
    {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
    }

    // method to verify the token and set the user to active to 1

    public function verifyEmail(){
        // search for the token in the database
        $sql = "SELECT * FROM users WHERE token = :token";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num > 0){
            // update the user to active
            $sql = "UPDATE users SET isactive = 1 WHERE token = :token";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':token', $this->token);
            $stmt->execute();
            // redirect to login page
            header('location: verfiedtoken.php?verified=1');
        }else{
            header('location: verfiedtoken.php?verified=0');
        }
    }

    public function create()
    {
        $this->fname = $this->sanitize($this->fname);
        $this->lname = $this->sanitize($this->lname);
        $this->email = $this->sanitize($this->email);
        $this->password = trim($this->password);
        $this->password_comfirm = trim($this->password_comfirm);
        if(empty($this->fname) || empty($this->lname) || empty($this->email) || empty($this->password))
        {
            echo "Please fill all the inputs";
        }
        elseif($this->password == $this->password_comfirm){
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num > 0){
                echo "Email already taken";
            }else{
            // :is placeholder
                $this->token = md5(rand().time());
                $sql = "INSERT INTO users (fname, lname, email, token,  password) VALUES (:fname, :lname, :email, :token, :password)";
                $stmt = $this->connect()->prepare($sql);
                // pass their value as input, and receives the output value
                $stmt->bindParam(':fname', $this->fname);
                $stmt->bindParam(':lname', $this->lname);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':token', $this->token);
                $stmt->bindParam(':password', $this->password);
                if($stmt->execute()){
                        $msg = 'Click on the activation link to verify your email. <br><br>
                        <a href="http://localhost/FIFA/verifytoken.php?token='.$this->token.'"> Click here to verify email</a>
                    ';

                        
                        require './PHPMailer/src/Exception.php';
                        require './PHPMailer/src/PHPMailer.php';
                        require './PHPMailer/src/SMTP.php';
                    //Import PHPMailer classes into the global namespace

                    //Create an instance; passing `true` enables exceptions
                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = $smtphost;                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = $smtpusername;                     //SMTP username
                        $mail->Password   = $smtppassword;                               //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        $mail->From = $smtpusername;
                        $mail->Sender = $smtpusername;
                        //Recipients
                        $mail->setFrom($smtpusername, 'Fifa 2022');
                        $mail->addAddress($this->email, 'Fifa team');     //Add a recipient
                        //$mail->addAddress('ellen@example.com');               //Name is optional
                        $mail->addReplyTo($smtpusername, 'fifa team');
                        //$mail->addCC('cc@example.com');
                        //$mail->addBCC('bcc@example.com');

                        //Attachments
                        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = "welcome to fifa 2022 ".$this->fname;
                        $mail->Body    = $this->email."<br>".$msg;
                        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                        header('Location: ./index.php?verify=true');
                    } catch (Exception $e) {
                        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    header('Location: ./index.php?verify=true');
                }
            }
        
        }else{
            echo "passwords dont match";
        }

    }
    // login method
    public function login()
    {
        $this->email = $this->sanitize($this->email);
        $this->password = trim($this->password);
        if(empty($this->email) || empty($this->password))
        {
            echo "Please fill all the inputs";
        }else{
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($this->password, $row['password'])){
                    if($row['isactive'] == 1){
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['fname'] = $row['fname'];
                        $_SESSION['lname'] = $row['lname'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['isadmin'] = $row['isadmin'];
                        $_SESSION['isactive'] = $row['isactive'];
                        $_SESSION['logged'] = true;
                        header('location: ./index.php');
                    }else{
                        echo "Please verify your email";
                    }
                }else{
                    echo "Wrong password";
                }
            }else{
                echo "Email not found";
            }
        }
    }
    // logout method
    public function logout()
    {
        session_destroy();
        header('location: ./login.php');
    }
    // method to update user to admin
    public function updateAdmin()
    {
        $sql = "UPDATE users SET isadmin = 1 WHERE email = :email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        if($stmt->execute()){
            header('location: ./admin.php');
        }
    }

}