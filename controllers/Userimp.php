
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

    private function sanitize($data)
    {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
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
                $sql = "INSERT INTO users (fname, lname, email,  password) VALUES (:fname, :lname, :email,  :password)";
                $stmt = $this->connect()->prepare($sql);
                // pass their value as input, and receives the output value
                $stmt->bindParam(':fname', $this->fname);
                $stmt->bindParam(':lname', $this->lname);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password', $this->password);
                if($stmt->execute()){
                        $token = md5(rand().time());
                        $msg = 'Click on the activation link to verify your email. <br><br>
                        <a href="http://e-class.imranechaibi.com/user_verificaiton.php?token='.$token.'"> Click here to verify email</a>
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
                        $mail->Host       = 'smtp.hostinger.com';                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = 'contact@imranechaibi.com';                     //SMTP username
                        $mail->Password   = 'hola1234HOLA@#';                               //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        $mail->From = 'contact@imranechaibi.com';
                        $mail->Sender = 'contact@imranechaibi.com';
                        //Recipients
                        $mail->setFrom('contact@imranechaibi.com', 'imranechaibi');
                        $mail->addAddress($email, 'E-class team');     //Add a recipient
                        //$mail->addAddress('ellen@example.com');               //Name is optional
                        $mail->addReplyTo('contact@imranechaibi.com', 'imrane chaibi');
                        //$mail->addCC('cc@example.com');
                        //$mail->addBCC('bcc@example.com');

                        //Attachments
                        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = $firstname;
                        $mail->Body    = $email."<br>".$msg;
                        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                        header('Location: ./index.php?verify=true');
                    } catch (Exception $e) {
                        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    header('location:index.php');
                }
            }
        
        }else{
            echo "passwords dont match";
        }

    }

}