<?php

global $wpdb;

$all_data = $_POST;

$software       = isset( $all_data['software'] ) ? $all_data['software'] : null;
$software_value = isset( $software ) ? 1 : 0;

$website       = isset( $all_data['website'] ) ? $all_data['website'] : null;
$website_value = isset( $website ) ? 1 : 0;

$mobile_app       = isset( $all_data['mobileApp'] ) ? $all_data['mobileApp'] : null;
$mobile_app_value = isset( $mobile_app ) ? 1 : 0;

$customBudget   = isset( $all_data['customBudget'] ) ? $all_data['customBudget'] : null;
$select_budget  = isset( $all_data['budget'] ) ? $all_data['budget'] : null;
$fullBudget     = $customBudget . " " . $select_budget;
$trimFullBudget = trim( $fullBudget );

$customDeadline  = isset( $all_data['customProjectDeadline'] ) ? $all_data['customProjectDeadline'] : null;
$select_deadline = isset( $all_data['deadline'] ) ? $all_data['deadline'] : null;
$fullDeadline    = $customDeadline . " " . $select_deadline;
$trimDeadline    = trim( $fullDeadline );

// Cleaned budget and deadline values
$cleanFullBudget   = str_replace( "Budget Planner", "", $trimFullBudget );
$cleanFullDeadline = str_replace( " Preferred Project Duration", "", $trimDeadline );

$requirement    = isset( $all_data['requirement'] ) ? $all_data['requirement'] : null;
$newRequirement = isset( $all_data['newRequirement'] ) ? $all_data['newRequirement'] : null;
// do foreach loop in $newRequirement and send it to $fullRequirement single string
$customRequirement = $requirement;
// $customRequirement = '';
if ( !empty( $newRequirement ) ) {
    foreach ( $newRequirement as $requirement ) {
        $customRequirement .= ', ' . $requirement;
    }
}
$fullRequirement = $customRequirement;

$first_name    = isset( $all_data['firstName'] ) ? $all_data['firstName'] : null;
$address       = isset( $all_data['address'] ) ? $all_data['address'] : null;
$email         = isset( $all_data['email'] ) ? $all_data['email'] : null;
$number        = isset( $all_data['number'] ) ? $all_data['number'] : null;
$watsAppNumber = isset( $all_data['watsAppNumber'] ) ? $all_data['watsAppNumber'] : null;

// Get the admin username dynamically
$admin_users    = get_users( array( 'role' => 'administrator' ) );
$admin_username = !empty( $admin_users ) ? $admin_users[0]->user_login : 'Admin';

// Get the admin email dynamically
// $admin_email = get_option( 'admin_email' );
$admin_email = 'rjshahjalal1010@gmail.com';

$data = [
    'first_name'  => $first_name,
    'address'     => $address,
    'email'       => $email,
    'phone'       => $number,
    'whatsapp'    => $watsAppNumber,
    'mobile_app'  => $mobile_app_value,
    'website'     => $website_value,
    'software'    => $software_value,
    'requirement' => $fullRequirement,
    'budget'      => $cleanFullBudget,
    'deadline'    => $cleanFullDeadline,
];

// Table name
$table_name = $wpdb->prefix . 'imjol_requirement_forms';

// Insert data into the database
if ( !empty( $first_name ) ) {

    // Check if the data was successfully inserted
    $insert = $wpdb->insert( $table_name, $data );

    // Check if the data was successfully inserted and send an email
    if ( $insert ) {
        // Set up email content
        $to      = $admin_email;
        $subject = 'New Form Submission from - ' . $first_name;

        // Email content
        $email_content = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Form Submission</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .logo {
                    text-align: center;
                    padding: 20px;
                    margin-bottom: 20px;
                }
                .logo img {
                    max-width: 150px;
                }
                table {
                    width: 100%;
                }
                table td {
                    padding: 10px;
                    border-bottom: 1px solid #ddd;
                }
                table td:first-child {
                    font-weight: bold;
                    width: 40%;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>
                    <img src='https://courselms.imjol.com/wp-content/uploads/2019/09/course-lms-logo.png' alt='Your Company Logo'>
                </div>
                <table>
                    <tr>
                        <td>Name:</td>
                        <td>$first_name</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>$email</td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td>$address</td>
                    </tr>
                    <tr>
                        <td>Requirement:</td>
                        <td>$fullRequirement</td>
                    </tr>
                    <tr>
                        <td>Budget:</td>
                        <td>$cleanFullBudget</td>
                    </tr>
                    <tr>
                        <td>Deadline:</td>
                        <td>$cleanFullDeadline</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>
    ";

        // Set headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= 'From: ' . $first_name; // Set the sender's email address

        // Send the email
        $mailSuccess = mail( $to, $subject, $email_content, $headers );

        if ( $mailSuccess ) {
            echo 'Email sent successfully!';
        } else {
            echo 'Failed to send email. Please try again.';
        }
    } else {
        echo 'Form not sent. Please try again.';
    }
}