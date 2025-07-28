<?php
/**
 * Generates a responsive HTML email template for OTP delivery.
 * @param string $email
 * @param string $fullname
 * @param string $otp
 * @return string
 */
function generateOtpEmailTemplate($email, $fullname, $otp) {
    $html = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your OTP Code</title>
        <style>
            body {
                background: #f4f4f7;
                margin: 0;
                padding: 0;
                font-family: 'Segoe UI', Arial, sans-serif;
            }
            .container {
                max-width: 480px;
                margin: 40px auto;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                padding: 32px 24px;
            }
            .header {
                text-align: center;
                margin-bottom: 24px;
            }
            .header h1 {
                color: #4f46e5;
                font-size: 2rem;
                margin: 0 0 8px 0;
            }
            .greeting {
                font-size: 1.1rem;
                color: #333;
                margin-bottom: 16px;
            }
            .otp-box {
                background: #f1f5ff;
                color: #1e293b;
                font-size: 2rem;
                font-weight: bold;
                letter-spacing: 8px;
                text-align: center;
                border-radius: 8px;
                padding: 18px 0;
                margin: 24px 0;
            }
            .info {
                color: #64748b;
                font-size: 0.98rem;
                margin-bottom: 24px;
                text-align: center;
            }
            .footer {
                text-align: center;
                color: #94a3b8;
                font-size: 0.9rem;
                margin-top: 32px;
            }
            @media (max-width: 600px) {
                .container {
                    padding: 16px 6px;
                }
                .header h1 {
                    font-size: 1.4rem;
                }
                .otp-box {
                    font-size: 1.3rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome to Cre8tive!</h1>
            </div>
            <div class="greeting">
                Hi <b>{$fullname}</b>,<br>
                Thank you for signing up. Please use the OTP below to verify your email address:
            </div>
            <div class="otp-box">{$otp}</div>
            <div class="info">
                This OTP is valid for 10 minutes. If you did not request this, please ignore this email.<br>
                <br>
                <b>Email:</b> {$email}
            </div>
            <div class="footer">
                &copy; " . date('Y') . " Cre8tive. All rights reserved.
            </div>
        </div>
    </body>
    </html>
    HTML;
    return $html;
} 