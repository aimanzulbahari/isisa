<?php
declare(strict_types=1);

/**
 * Mail helper (PHPMailer over SMTP).
 *
 * Settings come from the MAIL_* values in .env, via config('app.mail').
 *
 * Usage:
 *   require_once APP_PATH . '/mail/mail.php';
 *
 *   $result = sendMail('user@example.com', 'Ali', 'Welcome', '<p>Hello!</p>');
 *   if (!$result['success']) {
 *       echo $result['message'];
 *   }
 */

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

if (!function_exists('createMailer')) {
    /**
     * Build a PHPMailer object that is already set up with our SMTP settings.
     */
    function createMailer(): PHPMailer
    {
        $c    = config('app.mail');
        $mail = new PHPMailer(true); // true = throw exceptions on errors

        // --- SMTP connection ---
        $mail->isSMTP();
        $mail->Host = (string) $c['host'];
        $mail->Port = (int) $c['port'];

        // Only log in when a username is given (some local servers need no login)
        $mail->SMTPAuth = $c['username'] !== '';
        if ($mail->SMTPAuth) {
            $mail->Username = (string) $c['username'];
            $mail->Password = (string) $c['password'];
        }

        // Encryption: "tls" = STARTTLS (port 587), "ssl" = SMTPS (port 465)
        $mail->SMTPSecure = match (strtolower((string) $c['encryption'])) {
            'ssl'   => PHPMailer::ENCRYPTION_SMTPS,
            'tls'   => PHPMailer::ENCRYPTION_STARTTLS,
            default => '',
        };
        $mail->SMTPAutoTLS = $mail->SMTPSecure !== '';

        // Show SMTP conversation in the PHP error log, only in debug mode
        $mail->SMTPDebug   = config('app.debug') ? 2 : 0;
        $mail->Debugoutput = static fn(string $str) => error_log('[MAIL] ' . trim($str));

        // --- Message defaults ---
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isHTML(true);
        $mail->setFrom((string) $c['from'], (string) $c['from_name']);

        return $mail;
    }
}

if (!function_exists('sendMail')) {
    /**
     * Send one email.
     *
     * @param string               $to        Recipient email address.
     * @param string               $toName    Recipient name (can be empty).
     * @param string               $subject   Email subject.
     * @param string               $htmlBody  Email body in HTML.
     * @param array<string,mixed>  $options   Optional extras:
     *                                        - 'cc'          => string[]  CC addresses
     *                                        - 'bcc'         => string[]  BCC addresses
     *                                        - 'reply_to'    => string    Reply-To address
     *                                        - 'attachments' => string[]  File paths to attach
     *
     * @return array{success: bool, message: string}
     */
    function sendMail(string $to, string $toName, string $subject, string $htmlBody, array $options = []): array
    {
        // Stop early if the recipient address is not valid
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid recipient email address.'];
        }

        try {
            $mail = createMailer();

            $mail->addAddress($to, $toName);
            $mail->Subject = $subject;
            $mail->Body    = mailLayout($htmlBody);
            $mail->AltBody = trim(html_entity_decode(strip_tags($htmlBody))); // Plain-text fallback

            foreach ($options['cc'] ?? [] as $address) {
                $mail->addCC($address);
            }
            foreach ($options['bcc'] ?? [] as $address) {
                $mail->addBCC($address);
            }
            if (!empty($options['reply_to'])) {
                $mail->addReplyTo($options['reply_to']);
            }
            foreach ($options['attachments'] ?? [] as $file) {
                if (is_file($file)) {
                    $mail->addAttachment($file);
                }
            }

            $mail->send();
            return ['success' => true, 'message' => 'Email sent successfully.'];
        } catch (MailException $e) {
            // Log the real reason, but keep the message shown to users simple
            error_log('[MAIL] Send failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Unable to send the email. Please try again later.',
            ];
        }
    }
}

if (!function_exists('mailLayout')) {
    /**
     * Wrap the email content in a simple, clean HTML layout.
     * Inline styles are used because many email apps ignore <style> tags.
     */
    function mailLayout(string $content): string
    {
        $appName = htmlspecialchars((string) config('app.name'), ENT_QUOTES, 'UTF-8');
        $year    = date('Y');

        return <<<HTML
        <div style="background:#f4f6f8;padding:24px;font-family:Arial,Helvetica,sans-serif;color:#333;">
            <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;">
                <div style="background:#0d6efd;color:#ffffff;padding:16px 24px;font-size:18px;font-weight:bold;">
                    {$appName}
                </div>
                <div style="padding:24px;font-size:14px;line-height:1.6;">
                    {$content}
                </div>
                <div style="padding:16px 24px;font-size:12px;color:#888;border-top:1px solid #eee;">
                    This is an automated email. Please do not reply.<br>
                    &copy; {$year} {$appName}
                </div>
            </div>
        </div>
        HTML;
    }
}

if (!function_exists('sendPasswordResetMail')) {
    /**
     * Send a password reset link to a user.
     *
     * @param string $to       User email address.
     * @param string $fullname User full name.
     * @param string $resetUrl Full URL the user must open to reset the password.
     */
    function sendPasswordResetMail(string $to, string $fullname, string $resetUrl): array
    {
        $name = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
        $url  = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');

        $body = <<<HTML
        <p>Hello <strong>{$name}</strong>,</p>
        <p>We received a request to reset your password. Click the button below to choose a new one.</p>
        <p style="margin:24px 0;">
            <a href="{$url}" style="background:#0d6efd;color:#ffffff;padding:10px 20px;border-radius:4px;text-decoration:none;">
                Reset Password
            </a>
        </p>
        <p>If you did not ask for this, you can safely ignore this email.</p>
        HTML;

        return sendMail($to, $fullname, 'Reset your password', $body);
    }
}
