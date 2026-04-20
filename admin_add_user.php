<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.html"); exit(); }

require_once "backend/sqli.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect and sanitize basic inputs
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = (int)$_POST['phone']; // Database column is int(11)
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = $_POST['password'];
    $cpass = $_POST['cpass'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $country = mysqli_real_escape_string($conn, $_POST['countries']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $type = mysqli_real_escape_string($conn, $_POST['acc-type']);
    $discription = mysqli_real_escape_string($conn, $_POST['discription']);
    $balance = floatval($_POST['balance']);

    // 3. Validation
    if ($password !== $cpass) {
        die("Passwords do not match!");
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        die("An account with this email already exists.");
    }

    // 4. Password Hashing
    function generate_blowfish_salt() {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(16);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(16);
        } else {
            $bytes = '';
            for ($i = 0; $i < 16; $i++) {
                $bytes .= chr(mt_rand(0, 255));
            }
        }

        return '$2y$10$' . substr(strtr(base64_encode($bytes), '+', '.'), 0, 22);
    }

    function make_password_hash($password) {
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT);
        }
        return crypt($password, generate_blowfish_salt());
    }

    $hashed_password = make_password_hash($password);
    // 5. Prepare Insert Statement
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, phone, gender, password, address, country, state, zip, type, discription, balance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssisssssssssd",
        $fname,
        $lname,
        $email,
        $phone,
        $gender,
        $hashed_password,
        $address,
        $country,
        $state,
        $zip,
        $type,
        $discription,
        $balance
    );

    // 6. Execute and Redirect
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?success=user_added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f8f9fa; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #0d3b36; margin-bottom: 30px; text-align: center; }
        .form-row { display: flex; gap: 15px; margin-bottom: 20px; }
        .form-row > * { flex: 1; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .btn { background: #0d3b36; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 20px; }
        .btn:hover { background: #0a2e29; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #0d3b36; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <h1><i class="fas fa-user-plus"></i> Add New User</h1>

        <form action="admin_add_user.php" method="post">
            <div class="form-row">
                <input type="text" name="fname" required placeholder="First Name">
                <input type="text" name="lname" required placeholder="Last Name">
            </div>

            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="phone" placeholder="Phone Number" required>

            <div class="form-row">
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <input type="number" name="balance" step="0.01" placeholder="Initial Balance" value="0.00">
            </div>

            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="cpass" placeholder="Confirm Password" required>

            <input type="text" name="address" placeholder="Residential Address" required>

            <div class="form-row">
                <select name="countries" required>
                    <option value="">Select Country</option>
                    <option value="United States">United States</option>
                    <option value="Canada">Canada</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Australia">Australia</option>
                    <option value="Germany">Germany</option>
                    <option value="France">France</option>
                    <option value="Italy">Italy</option>
                    <option value="Spain">Spain</option>
                    <option value="Netherlands">Netherlands</option>
                    <option value="Belgium">Belgium</option>
                    <option value="Switzerland">Switzerland</option>
                    <option value="Austria">Austria</option>
                    <option value="Sweden">Sweden</option>
                    <option value="Norway">Norway</option>
                    <option value="Denmark">Denmark</option>
                    <option value="Finland">Finland</option>
                    <option value="Ireland">Ireland</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Greece">Greece</option>
                    <option value="Poland">Poland</option>
                    <option value="Czech Republic">Czech Republic</option>
                    <option value="Hungary">Hungary</option>
                    <option value="Slovakia">Slovakia</option>
                    <option value="Slovenia">Slovenia</option>
                    <option value="Croatia">Croatia</option>
                    <option value="Romania">Romania</option>
                    <option value="Bulgaria">Bulgaria</option>
                    <option value="Serbia">Serbia</option>
                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                    <option value="Montenegro">Montenegro</option>
                    <option value="North Macedonia">North Macedonia</option>
                    <option value="Albania">Albania</option>
                    <option value="Kosovo">Kosovo</option>
                    <option value="Turkey">Turkey</option>
                    <option value="Russia">Russia</option>
                    <option value="Ukraine">Ukraine</option>
                    <option value="Belarus">Belarus</option>
                    <option value="Moldova">Moldova</option>
                    <option value="Estonia">Estonia</option>
                    <option value="Latvia">Latvia</option>
                    <option value="Lithuania">Lithuania</option>
                    <option value="Japan">Japan</option>
                    <option value="South Korea">South Korea</option>
                    <option value="China">China</option>
                    <option value="India">India</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Bhutan">Bhutan</option>
                    <option value="Maldives">Maldives</option>
                    <option value="Thailand">Thailand</option>
                    <option value="Vietnam">Vietnam</option>
                    <option value="Cambodia">Cambodia</option>
                    <option value="Laos">Laos</option>
                    <option value="Myanmar">Myanmar</option>
                    <option value="Malaysia">Malaysia</option>
                    <option value="Singapore">Singapore</option>
                    <option value="Indonesia">Indonesia</option>
                    <option value="Philippines">Philippines</option>
                    <option value="Brunei">Brunei</option>
                    <option value="East Timor">East Timor</option>
                    <option value="New Zealand">New Zealand</option>
                    <option value="Fiji">Fiji</option>
                    <option value="Papua New Guinea">Papua New Guinea</option>
                    <option value="Solomon Islands">Solomon Islands</option>
                    <option value="Vanuatu">Vanuatu</option>
                    <option value="Samoa">Samoa</option>
                    <option value="Tonga">Tonga</option>
                    <option value="Tuvalu">Tuvalu</option>
                    <option value="Kiribati">Kiribati</option>
                    <option value="Marshall Islands">Marshall Islands</option>
                    <option value="Micronesia">Micronesia</option>
                    <option value="Palau">Palau</option>
                    <option value="Nauru">Nauru</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Brazil">Brazil</option>
                    <option value="Chile">Chile</option>
                    <option value="Colombia">Colombia</option>
                    <option value="Ecuador">Ecuador</option>
                    <option value="Peru">Peru</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Bolivia">Bolivia</option>
                    <option value="Paraguay">Paraguay</option>
                    <option value="Uruguay">Uruguay</option>
                    <option value="Guyana">Guyana</option>
                    <option value="Suriname">Suriname</option>
                    <option value="French Guiana">French Guiana</option>
                    <option value="Mexico">Mexico</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Belize">Belize</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Nicaragua">Nicaragua</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Panama">Panama</option>
                    <option value="Cuba">Cuba</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Dominican Republic">Dominican Republic</option>
                    <option value="Jamaica">Jamaica</option>
                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                    <option value="Saint Lucia">Saint Lucia</option>
                    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                    <option value="Grenada">Grenada</option>
                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                    <option value="Dominica">Dominica</option>
                    <option value="Egypt">Egypt</option>
                    <option value="Libya">Libya</option>
                    <option value="Tunisia">Tunisia</option>
                    <option value="Algeria">Algeria</option>
                    <option value="Morocco">Morocco</option>
                    <option value="Western Sahara">Western Sahara</option>
                    <option value="Mauritania">Mauritania</option>
                    <option value="Mali">Mali</option>
                    <option value="Niger">Niger</option>
                    <option value="Chad">Chad</option>
                    <option value="Sudan">Sudan</option>
                    <option value="South Sudan">South Sudan</option>
                    <option value="Eritrea">Eritrea</option>
                    <option value="Djibouti">Djibouti</option>
                    <option value="Somalia">Somalia</option>
                    <option value="Ethiopia">Ethiopia</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Tanzania">Tanzania</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Rwanda">Rwanda</option>
                    <option value="Burundi">Burundi</option>
                    <option value="South Africa">South Africa</option>
                    <option value="Lesotho">Lesotho</option>
                    <option value="Botswana">Botswana</option>
                    <option value="Swaziland">Swaziland</option>
                    <option value="Namibia">Namibia</option>
                    <option value="Zimbabwe">Zimbabwe</option>
                    <option value="Zambia">Zambia</option>
                    <option value="Malawi">Malawi</option>
                    <option value="Mozambique">Mozambique</option>
                    <option value="Angola">Angola</option>
                    <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                    <option value="Republic of the Congo">Republic of the Congo</option>
                    <option value="Central African Republic">Central African Republic</option>
                    <option value="Gabon">Gabon</option>
                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                    <option value="Cameroon">Cameroon</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Benin">Benin</option>
                    <option value="Togo">Togo</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Cote d'Ivoire">Cote d'Ivoire</option>
                    <option value="Liberia">Liberia</option>
                    <option value="Sierra Leone">Sierra Leone</option>
                    <option value="Guinea">Guinea</option>
                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Gambia">Gambia</option>
                    <option value="Cape Verde">Cape Verde</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="Seychelles">Seychelles</option>
                    <option value="Comoros">Comoros</option>
                    <option value="Madagascar">Madagascar</option>
                    <option value="Mayotte">Mayotte</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Saudi Arabia">Saudi Arabia</option>
                    <option value="United Arab Emirates">United Arab Emirates</option>
                    <option value="Qatar">Qatar</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Bahrain">Bahrain</option>
                    <option value="Oman">Oman</option>
                    <option value="Yemen">Yemen</option>
                    <option value="Jordan">Jordan</option>
                    <option value="Lebanon">Lebanon</option>
                    <option value="Syria">Syria</option>
                    <option value="Iraq">Iraq</option>
                    <option value="Iran">Iran</option>
                    <option value="Israel">Israel</option>
                    <option value="Palestine">Palestine</option>
                    <option value="Cyprus">Cyprus</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Armenia">Armenia</option>
                    <option value="Azerbaijan">Azerbaijan</option>
                    <option value="Kazakhstan">Kazakhstan</option>
                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                    <option value="Tajikistan">Tajikistan</option>
                    <option value="Turkmenistan">Turkmenistan</option>
                    <option value="Uzbekistan">Uzbekistan</option>
                </select>
                <input type="text" name="state" placeholder="State/Province" required>
            </div>

            <div class="form-row">
                <input type="text" name="zip" placeholder="ZIP/Postal Code" required>
                <select name="acc-type" required>
                    <option value="">Account Type</option>
                    <option value="Personal">Personal</option>
                    <option value="Business">Business</option>
                    <option value="Premium">Premium</option>
                </select>
            </div>

            <textarea name="discription" placeholder="Account Description (Optional)" rows="3"></textarea>

            <button type="submit" class="btn">Create User Account</button>
        </form>
    </div>
</body>
</html>